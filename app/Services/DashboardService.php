<?php

namespace App\Services;

use App\Models\Project;
use App\Models\WbsItem;
use App\Models\TimelineItem;

class DashboardService
{
    public function getDashboardData($user)
    {
        $projectQuery = $this->buildProjectQuery($user);

        return [
            'showCards' => count($this->getCards($user)) > 0,
            'cards' => $this->getCards($user),

            // statistik
            'totalProjects' => $this->countProjects($projectQuery),
            'draftProjects' => $this->countByStatus($projectQuery, 'draft'),
            'submittedProjects' => $this->countByStatus($projectQuery, 'submitted'),
            'planningProjects' => $this->countPlanning($projectQuery),
            'completedProjects' => $this->countByStatus($projectQuery, 'completed'),

            // recent
            'recentProjects' => $this->getRecentProjects($projectQuery),

            // next actions
            'nextActions' => $this->getNextActions($user),
        ];
    }

    // Query blueprint for projects table
    private function buildProjectQuery($user)
    {
        $query = Project::query();

        if (strtolower($user->role) === 'project manager') {
            $query->where('owner_id', $user->id);
        }

        return $query;
    }

    // check recent projects
    private function getRecentProjects($query)
    {
        return (clone $query)
            ->with(['owner', 'manager'])
            ->latest('updated_at')
            ->take(3)
            ->get()
            ->map(function ($proj) {
                return $this->formatProject($proj);
            });
    }

    // count project for total-project card
    private function countProjects($query)
    {
        return (clone $query)->count();
    }

    // count project for total-project card
    private function countByStatus($query, $status)
    {
        return (clone $query)->where('status', $status)->count();
    }

    // count project for total-planning project card
    private function countPlanning($query)
    {
        return (clone $query)
            ->whereIn('status', ['approved', 'planning'])
            ->count();
    }

    // next acction (suggestion per role what todo)
    private function getNextActions($user)
    {
        $actions = [];

        if ($this->isManager($user)) {
            $actions = array_merge($actions, $this->getManagerActions());
        }

        $actions = array_merge($actions, $this->getPlanningActions($user));

        return $actions;
    }

    private function getManagerActions()
    {
        $nextActions = [];

        $submittedProjectsList = Project::where('status', 'submitted')->get();

        foreach ($submittedProjectsList as $proj) {
            $nextActions[] = [
                'title' => 'Review Proposal: ' . $proj->title,
                'subtext' => 'Menunggu Persetujuan Anda',
                'link' => route('projects.edit', $proj->id),
                'action_text' => 'Tinjau Sekarang',
                'color' => 'rose',
                'icon' => 'fa-file-signature',
            ];
        }

        return $nextActions;
    }

    private function getPlanningActions($user)
    {
        $nextActions = [];

        $planningProjectsList = Project::where('status', 'planning')->get();

        foreach ($planningProjectsList as $proj) {

            if (!$proj->scope || strtolower($proj->scope->status) !== 'finalized') {
                if ($this->isManager($user)) {
                    $nextActions[] = [
                        'title' => 'Finalisasi Scope: ' . $proj->title,
                        'subtext' => 'Tahap Perencanaan Scope',
                        'link' => route('projects.scope.show', $proj->id),
                        'action_text' => 'Lengkapi Scope',
                        'color' => 'blue',
                        'icon' => 'fa-compass',
                    ];
                }
            } elseif (
                !$proj->wbsItems()->exists() ||
                WbsItem::where('project_id', $proj->id)->where('status', 'draft')->exists()
            ) {
                if ($this->isPMO($user)) {
                    $nextActions[] = [
                        'title' => 'Susun WBS Proyek: ' . $proj->title,
                        'subtext' => 'Tugas/WBS Belum Selesai',
                        'link' => route('projects.wbs.show', $proj->id),
                        'action_text' => 'Kelola WBS',
                        'color' => 'amber',
                        'icon' => 'fa-sitemap',
                    ];
                }
            } elseif (
                !$proj->timelineItems()->exists() ||
                TimelineItem::where('project_id', $proj->id)->where('status', 'draft')->exists()
            ) {
                if ($this->isPMO($user)) {
                    $nextActions[] = [
                        'title' => 'Jadwalkan Timeline: ' . $proj->title,
                        'subtext' => 'Timeline Belum Final',
                        'link' => route('projects.timeline.show', $proj->id),
                        'action_text' => 'Buka Gantt Chart',
                        'color' => 'indigo',
                        'icon' => 'fa-calendar-days',
                    ];
                }
            }
        }
        return $nextActions;
    }

    private function isManager($user)
    {
        return strtolower($user->role) === 'manager';
    }

    private function isPMO($user)
    {
        return in_array(strtolower($user->role), ['pmo', 'project management officer']);
    }

    // Status card based on user auth
    public function getCards($user)
    {
        $role = $user->role;

        $roleCards = [
            'Project Management Officer' => [
                'total-proyek',
                'total-draft',
                'total-on-request',
                'total-on-planning',
                'total-done'
            ],

            'Manager' => [
                'total-proyek',
                'total-draft',
                'total-on-request',
                'total-on-planning',
                'total-done'
            ],

            'Project Manager' => [
                'total-proyek',
                'total-draft',
                'total-on-request',
                'total-on-planning',
                'total-done'
            ],

            'IT' => [
                'total-proyek',
                'total-done'
            ],
        ];

        return $roleCards[$role] ?? [];
    }

    private function formatProject($proj)
    {
        // Owner name
        $name = $proj->owner ? $proj->owner->name : 'System';

        // Initials
        $words = explode(' ', $name);
        if (count($words) >= 2) {
            $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        } else {
            $initials = strtoupper(substr($name, 0, 2));
        }

        // Category
        $category = __('General Project');

        if (
            stripos($proj->title, 'it') !== false ||
            stripos($proj->title, 'cloud') !== false ||
            stripos($proj->title, 'software') !== false ||
            stripos($proj->title, 'sistem') !== false
        ) {
            $category = __('Infrastruktur IT');
        } elseif (
            stripos($proj->title, 'gudang') !== false ||
            stripos($proj->title, 'logistik') !== false ||
            stripos($proj->title, 'distribusi') !== false
        ) {
            $category = __('Logistik & Distribusi');
        } elseif (
            stripos($proj->title, 'hr') !== false ||
            stripos($proj->title, 'sdm') !== false ||
            stripos($proj->title, 'human') !== false
        ) {
            $category = __('Sumber Daya Manusia');
        }

        // Activity
        $activityText = __('Mengupdate status proyek');
        
        // Status label + style
        $statusLabel = ucfirst($proj->status);
        $statusClass = 'bg-rose-50 text-rose-700 border-rose-100';

        switch ($proj->status) {
            case 'draft':
                $statusLabel = __('Draf');
                $statusClass = 'bg-slate-100 text-slate-700 border-slate-200';
                break;

            case 'submitted':
                $statusLabel = __('Dalam Review');
                $statusClass = 'bg-blue-50 text-blue-700 border-blue-100';
                break;

            case 'approved':
            case 'planning':
                $statusLabel = __('Planning');
                $statusClass = 'bg-indigo-50 text-indigo-700 border-indigo-100';
                break;

            case 'completed':
                $statusLabel = __('Selesai');
                $statusClass = 'bg-emerald-50 text-emerald-700 border-emerald-100';
                break;
        }

        return [
            // sebelumnya
            'title' => $proj->title,
            'category' => $category,
            'name' => $name,
            'initials' => $initials,
            'activity' => $activityText,
            'time' => $proj->updated_at->diffForHumans(),

            // tambahan
            'status_label' => $statusLabel,
            'status_class' => $statusClass,
        ];
    }
}
