<?php

namespace App\Livewire;

use App\Models\Alumni;
use App\Models\ContentStatusLog;
use App\Models\Galeri;
use App\Models\KaryaSiswa;
use App\Models\Prestasi;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Dashboard - CMS Jurusan RPL')]
class Dashboard extends Component
{
    /**
     * Get statistics for dashboard widgets
     */
    public function getWidgetData()
    {
        return [
            'total_karya_siswa' => KaryaSiswa::count(),
            'total_prestasi' => Prestasi::count(),
            'total_alumni' => Alumni::count(),
            'total_galeri' => Galeri::count(),
            'konten_draft' => $this->countByStatus('draft'),
            'konten_review' => $this->countByStatus('review'),
        ];
    }

    /**
     * Count content by status across all models
     */
    protected function countByStatus(string $status): int
    {
        return KaryaSiswa::where('status', $status)->count()
            + Prestasi::where('status', $status)->count()
            + Alumni::where('status', $status)->count()
            + Galeri::where('status', $status)->count();
    }

    /**
     * Get recent activity from status logs
     */
    public function getRecentActivity()
    {
        return ContentStatusLog::with(['user', 'loggable'])
            ->latest('created_at')
            ->take(5)
            ->get()
            ->map(function ($log) {
                $modelName = class_basename($log->loggable_type);
                $modelTitle = $log->loggable->judul ?? $log->loggable->nama ?? 'Item';
                
                return [
                    'user_name' => $log->user->name,
                    'action' => $this->getActionText($log),
                    'model' => $this->translateModel($modelName),
                    'title' => $modelTitle,
                    'time' => $log->created_at->diffForHumans(),
                    'from_status' => $log->getFromStatusLabelAttribute(),
                    'to_status' => $log->getToStatusLabelAttribute(),
                ];
            });
    }

    /**
     * Get action text from log
     */
    protected function getActionText(ContentStatusLog $log): string
    {
        if ($log->from_status === null) {
            return 'membuat';
        }
        
        return match($log->to_status) {
            'review' => 'mengirim untuk review',
            'published' => 'mempublikasi',
            'archived' => 'mengarsipkan',
            'draft' => 'mengembalikan ke draft',
            default => 'mengubah status',
        };
    }

    /**
     * Translate model name to Indonesian
     */
    protected function translateModel(string $model): string
    {
        return match($model) {
            'KaryaSiswa' => 'Karya Siswa',
            'Prestasi' => 'Prestasi',
            'Galeri' => 'Galeri',
            'Alumni' => 'Alumni',
            default => $model,
        };
    }

    /**
     * Get content status summary
     */
    public function getContentStatusSummary()
    {
        $models = [KaryaSiswa::class, Prestasi::class, Alumni::class, Galeri::class];
        $summary = [];

        foreach ($models as $model) {
            $modelName = class_basename($model);
            $summary[$this->translateModel($modelName)] = [
                'draft' => $model::draft()->count(),
                'review' => $model::inReview()->count(),
                'published' => $model::published()->count(),
                'archived' => $model::archived()->count(),
            ];
        }

        return $summary;
    }

    public function render()
    {
        return view('livewire.dashboard', [
            'widgets' => $this->getWidgetData(),
            'recentActivity' => $this->getRecentActivity(),
            'statusSummary' => $this->getContentStatusSummary(),
        ]);
    }
}
