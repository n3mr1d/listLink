<style>
    .admin-nav{display:flex;gap:.35rem;overflow-x:auto;padding-bottom:.75rem;margin-bottom:1.5rem;border-bottom:1px solid var(--color-gh-border);}
    .admin-nav::-webkit-scrollbar{display:none;}
    .admin-nav a{padding:.45rem .75rem;border-radius:.4rem;font-size:.65rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;white-space:nowrap;text-decoration:none;border:1px solid transparent;transition:color .15s,border-color .15s;}
    .admin-nav a.active{background:var(--color-gh-accent);color:#0d1117;border-color:var(--color-gh-accent);}
    .admin-nav a:not(.active){color:var(--color-gh-dim);border-color:var(--color-gh-border);}
    .admin-nav a:not(.active):hover{color:#fff;border-color:var(--color-gh-dim);}
    .admin-table{width:100%;border-collapse:collapse;}
    .admin-table th{padding:.55rem 1rem;font-size:.6rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--color-gh-dim);border-bottom:1px solid var(--color-gh-border);text-align:left;white-space:nowrap;}
    .admin-table td{padding:.65rem 1rem;border-bottom:1px solid rgba(48,54,61,.35);vertical-align:middle;}
    .admin-table tr:last-child td{border-bottom:none;}
    .admin-table tr:hover td{background:rgba(255,255,255,.015);}
    .panel{border:1px solid var(--color-gh-border);border-radius:.6rem;overflow:hidden;margin-bottom:1.5rem;}
    .panel-head{padding:.7rem 1rem;border-bottom:1px solid var(--color-gh-border);display:flex;align-items:center;gap:.4rem;font-size:.65rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#fff;}
    .panel-head svg{flex-shrink:0;color:var(--color-gh-accent);}
    .btn-sm{display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:.35rem;border:1px solid var(--color-gh-border);background:none;cursor:pointer;transition:all .15s;}
    .btn-sm:hover{border-color:var(--color-gh-dim);}
    .btn-sm svg{width:12px;height:12px;}
    .filter-bar{display:flex;gap:.35rem;margin-bottom:1.25rem;overflow-x:auto;}
    .filter-bar::-webkit-scrollbar{display:none;}
    .filter-bar a{padding:.4rem .7rem;border-radius:.35rem;font-size:.6rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;border:1px solid var(--color-gh-border);color:var(--color-gh-dim);text-decoration:none;white-space:nowrap;transition:all .15s;}
    .filter-bar a.active{background:#fff;color:#0d1117;border-color:#fff;}
    .filter-bar a:not(.active):hover{color:#fff;border-color:var(--color-gh-dim);}
    .status-badge{display:inline-flex;align-items:center;gap:.3rem;padding:.15rem .45rem;border-radius:.3rem;font-size:.55rem;font-weight:800;text-transform:uppercase;letter-spacing:.05em;border:1px solid;}
    .sb-online{color:#4ade80;border-color:rgba(74,222,128,.25);}
    .sb-offline{color:#f87171;border-color:rgba(248,113,113,.25);}
    .sb-timeout{color:#fb923c;border-color:rgba(251,146,60,.25);}
    .sb-pending{color:#fb923c;border-color:rgba(251,146,60,.25);}
    .sb-active{color:#4ade80;border-color:rgba(74,222,128,.25);}
    .sb-rejected{color:#f87171;border-color:rgba(248,113,113,.25);}
    .sb-expired{color:var(--color-gh-dim);border-color:var(--color-gh-border);}
    .sb-success{color:#4ade80;border-color:rgba(74,222,128,.25);}
    .sb-failed{color:#f87171;border-color:rgba(248,113,113,.25);}
    .sb-skipped{color:var(--color-gh-dim);border-color:var(--color-gh-border);}
    .sb-unknown{color:var(--color-gh-dim);border-color:var(--color-gh-border);}
    .empty-state{padding:3rem 1rem;text-align:center;color:var(--color-gh-dim);opacity:.4;}
    .empty-state svg{display:block;margin:0 auto .5rem;}
    .empty-state p{font-size:.75rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;margin:0;}
    .admin-header{margin-bottom:1.5rem;}
    .admin-header h1{font-size:1.4rem;font-weight:900;color:#fff;margin:0 0 .25rem;letter-spacing:-.02em;}
    .admin-header p{font-size:.75rem;color:var(--color-gh-dim);margin:0;}
    @media(max-width:640px){
        .admin-table th:nth-child(n+3),.admin-table td:nth-child(n+3){display:none;}
        .hide-mobile{display:none!important;}
    }
</style>

<nav class="admin-nav">
    @php
        $navItems = [
            ['Search', route('admin.search'), request()->routeIs('admin.search')],
            ['Insights', route('admin.dashboard'), request()->routeIs('admin.dashboard')],
            ['Registry', route('admin.links'), request()->routeIs('admin.links')],
            ['Offline', route('admin.offline-links'), request()->routeIs('admin.offline-links')],
            ['Ad Queue', route('admin.ads'), request()->routeIs('admin.ads')],
            ['Uptime', route('admin.uptime-logs'), request()->routeIs('admin.uptime-logs')],
            ['Security', route('admin.blacklist'), request()->routeIs('admin.blacklist')],
            ['Crawler', route('admin.crawler.index'), request()->routeIs('admin.crawler.*')],
            ['Extraction', route('admin.email-crawler.index'), request()->routeIs('admin.email-crawler.*')],
            ['Users', route('admin.users.index'), request()->routeIs('admin.users.*')]
        ];
    @endphp
    @foreach($navItems as $item)
        <a href="{{ $item[1] }}" class="{{ $item[2] ? 'active' : '' }}">{{ $item[0] }}</a>
    @endforeach
</nav>
