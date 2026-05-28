<?php

namespace App\Services;

use App\Models\Project;
use App\Models\WbsItem;
use App\Models\TimelineItem;

class DashboardService
{
    public function getDashboardData($user)
    {
        // Role check
        $isPM = strtolower($user->role) === 'project manager';
        $isManager = strtolower($user->role) === 'manager';
        $isPMO = in_array(strtolower($user->role), ['pmo', 'project management officer']);

        // Base query
        $projectQuery = Project::query();

        if ($isPM) {
            $projectQuery->where('owner_id', $user->id);
        }

        // Statistik
        $totalProjects = (clone $projectQuery)->count();
        $draftProjects = (clone $projectQuery)->where('status', 'draft')->count();
        $submittedProjects = (clone $projectQuery)->where('status', 'submitted')->count();
        $planningProjects = (clone $projectQuery)->whereIn('status', ['approved', 'planning'])->count();
        $completedProjects = (clone $projectQuery)->where('status', 'completed')->count();

        // Recent projects
        $recentProjects = (clone $projectQuery)
            ->with(['owner', 'manager'])
            ->latest('updated_at')
            ->take(3)
            ->get();

        // Next Actions
        $nextActions = [];

        if ($isManager) {
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
        }

        $planningProjectsList = Project::where('status', 'planning')->get();

        foreach ($planningProjectsList as $proj) {

            if (!$proj->scope || strtolower($proj->scope->status) !== 'finalized') {
                if ($isManager) {
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
                if ($isPMO) {
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
                if ($isPMO) {
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

        // Cards
        $allowedRoles = ['project manager', 'manager', 'project management officer'];
        $showCards = in_array(strtolower($user->role), $allowedRoles);

        $cards = [
            'total-proyek',
            'total-draft',
            'total-on-request',
            'total-on-planning',
            'total-done'
        ];

        return [
            'showCards' => $showCards,
            'cards' => $cards,
            'totalProjects' => $totalProjects,
            'draftProjects' => $draftProjects,
            'submittedProjects' => $submittedProjects,
            'planningProjects' => $planningProjects,
            'completedProjects' => $completedProjects,
            'recentProjects' => $recentProjects,
            'nextActions' => $nextActions,
        ];
    }
}