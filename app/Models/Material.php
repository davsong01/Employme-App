<?php

namespace App\Models;
use App\Models\Program;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $guarded = [];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    public function getVideoPlayerAttribute(): ?array
    {
        if ($this->type !== 'video') {
            return null;
        }

        $parts = parse_url($this->file);
        if (! is_array($parts) || ! in_array(strtolower($parts['scheme'] ?? ''), ['http', 'https'], true)) {
            return null;
        }

        $host = strtolower($parts['host'] ?? '');
        $path = $parts['path'] ?? '';
        $videoId = null;
        if (in_array($host, ['youtu.be', 'www.youtu.be'], true)) {
            $videoId = trim($path, '/');
        } elseif (in_array($host, ['youtube.com', 'www.youtube.com', 'm.youtube.com', 'youtube-nocookie.com', 'www.youtube-nocookie.com'], true)) {
            parse_str($parts['query'] ?? '', $query);
            if ($path === '/watch') {
                $videoId = $query['v'] ?? null;
            } elseif (preg_match('~^/(?:embed|shorts|live)/([^/]+)~', $path, $matches)) {
                $videoId = $matches[1];
            }
        }

        if (is_string($videoId) && preg_match('/^[A-Za-z0-9_-]{11}$/D', $videoId)) {
            return ['kind' => 'iframe', 'url' => 'https://www.youtube-nocookie.com/embed/' . $videoId];
        }

        if (in_array($host, ['vimeo.com', 'www.vimeo.com', 'player.vimeo.com'], true)
            && preg_match('~^/(?:video/)?([0-9]+)/?$~', $path, $matches)) {
            return ['kind' => 'iframe', 'url' => 'https://player.vimeo.com/video/' . $matches[1]];
        }

        if (preg_match('/\.(mp4|webm|ogv)$/i', $path)) {
            return ['kind' => 'video', 'url' => $this->file];
        }

        return null;
    }

    public function program(){
        return $this->belongsTo(Program::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
