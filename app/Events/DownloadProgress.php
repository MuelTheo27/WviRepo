<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DownloadProgress implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $userId;
    public string $downloadId;
    public int $completed;
    public int $failed;
    public int $progress;
    public string $status;

    public function __construct(
        int $userId,
        string $downloadId,
        int $completed,
        int $failed,
        int $progress = 0,
        string $status = 'starting'
    ) {
        $this->userId = $userId;
        $this->downloadId = $downloadId;
        $this->completed = $completed;
        $this->failed = $failed;
        $this->progress = $progress;
        $this->status = $status;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('download-progress.' . $this->userId);
    }

    public function broadcastWith(): array
    {
        return [
            'userId' => $this->userId,
            'downloadId' => $this->downloadId,
            'completed' => $this->completed,
            'failed' => $this->failed,
            'progress' => $this->progress,
            'status' => $this->status,
        ];
    }

    public function broadcastAs(): string
    {
        return 'DownloadProgressEvent';
    }
}
