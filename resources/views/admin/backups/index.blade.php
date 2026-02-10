@extends('layouts.app')

@section('content')
<div class="container" style="max-width:1000px; margin:0 auto; padding:20px;">
    
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
        <h1 style="margin:0; font-size:24px;">Database Backups</h1>
        <form action="{{ route('admin.backups.store') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary" style="background:var(--accent); color:#fff; border:none; padding:10px 20px; border-radius:6px; cursor:pointer; font-weight:600;">
                + Create New Backup
            </button>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="background:rgba(46, 204, 113, 0.2); color:#2ecc71; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid rgba(46, 204, 113, 0.3);">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error" style="background:rgba(231, 76, 60, 0.2); color:#e74c3c; padding:15px; border-radius:6px; margin-bottom:20px; border:1px solid rgba(231, 76, 60, 0.3);">
            {{ session('error') }}
        </div>
    @endif

    <div class="card" style="background:rgba(255,255,255,0.05); border-radius:12px; overflow:hidden;">
        <table style="width:100%; border-collapse:collapse; text-align:left;">
            <thead>
                <tr style="background:rgba(255,255,255,0.05); border-bottom:1px solid rgba(255,255,255,0.1);">
                    <th style="padding:16px; font-weight:600; font-size:14px; color:var(--muted);">Filename</th>
                    <th style="padding:16px; font-weight:600; font-size:14px; color:var(--muted);">Size</th>
                    <th style="padding:16px; font-weight:600; font-size:14px; color:var(--muted);">Created At</th>
                    <th style="padding:16px; font-weight:600; font-size:14px; color:var(--muted); text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($backups as $backup)
                    <tr style="border-bottom:1px solid rgba(255,255,255,0.05); transition:background 0.2s;">
                        <td style="padding:16px; font-weight:500;">
                            📄 {{ $backup['filename'] }}
                        </td>
                        <td style="padding:16px; color:var(--muted); font-size:14px;">
                            {{ $backup['size'] }}
                        </td>
                        <td style="padding:16px; color:var(--muted); font-size:14px;">
                            {{ $backup['created_at']->format('M d, Y H:i:s') }}
                            <div style="font-size:11px; opacity:0.6;">{{ $backup['created_at']->diffForHumans() }}</div>
                        </td>
                        <td style="padding:16px; text-align:right;">
                            <a href="{{ route('admin.backups.download', $backup['filename']) }}" class="btn" style="display:inline-block; margin-right:8px; text-decoration:none; color:var(--accent); background:rgba(52, 152, 219, 0.1); padding:6px 12px; border-radius:4px; font-size:12px;">
                                ⬇ Download
                            </a>
                            <form action="{{ route('admin.backups.destroy', $backup['filename']) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this backup?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn" style="background:rgba(231, 76, 60, 0.1); color:#e74c3c; border:none; padding:6px 12px; border-radius:4px; font-size:12px; cursor:pointer;">
                                    🗑 Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="padding:40px; text-align:center; color:var(--muted);">
                            No backups found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:24px;">
        <details style="background:rgba(255,255,255,0.02); border:1px solid rgba(255,255,255,0.05); border-radius:8px; padding:16px;">
            <summary style="cursor:pointer; font-weight:600; color:var(--muted);"> Troubleshooting: "Failed to create backup"</summary>
            <div style="margin-top:12px; font-size:14px; color:var(--muted); line-height:1.6;">
                <p>If you see a "Failed to create backup" error, it likely means the <code>mysqldump</code> command is not recognized by the server.</p>
                
                <h4 style="color:#fff; margin-bottom:8px;">How to fix on Windows (XAMPP/Laragon):</h4>
                <ol style="margin-left:20px;">
                    <li>Find where <code>mysqldump.exe</code> is located. usually:
                        <ul>
                            <li>XAMPP: <code>C:\xampp\mysql\bin</code></li>
                            <li>Laragon: <code>C:\laragon\bin\mysql\mysql-x.x.x-winx64\bin</code></li>
                        </ul>
                    </li>
                    <li>Add this path to your <strong>System Environment Variables (PATH)</strong>.</li>
                    <li>Restart your computer (or at least the web server/terminal) for changes to take effect.</li>
                </ol>

                <h4 style="color:#fff; margin-bottom:8px; margin-top:16px;">How to fix on Linux/Production:</h4>
                <ul style="margin-left:20px;">
                    <li>Ensure <code>mysql-client</code> or <code>mariadb-client</code> is installed: <br><code>sudo apt-get install mysql-client</code></li>
                    <li>Verify <code>mysqldump</code> runs in the terminal.</li>
                </ul>
            </div>
        </details>
    </div>
</div>
@endsection
