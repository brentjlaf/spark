<?php
// Shared Tailwind assets for admin-facing pages.
?>
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
    theme: {
        extend: {
            colors: {
                primary: {
                    50: '#eef2ff',
                    100: '#e0e7ff',
                    200: '#c7d2fe',
                    300: '#a5b4fc',
                    400: '#818cf8',
                    500: '#6366f1',
                    600: '#4f46e5',
                    700: '#4338ca',
                    800: '#3730a3',
                    900: '#312e81'
                }
            },
            fontFamily: {
                sans: ['Poppins', 'Inter', 'system-ui', 'sans-serif'],
                mono: ['JetBrains Mono', 'Menlo', 'monospace']
            }
        }
    }
};
</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style type="text/tailwindcss">
@layer base {
  body { @apply bg-slate-50 text-slate-900 font-sans; }
  a { @apply text-primary-600 hover:text-primary-700; }
  .sr-only { @apply sr-only; }
}

@layer components {
  .admin-container { @apply min-h-screen flex bg-slate-50 text-slate-900; }
  .sidebar { @apply fixed md:static inset-y-0 left-0 w-72 bg-white border-r border-slate-200 shadow-lg md:shadow-none overflow-y-auto transform -translate-x-full md:translate-x-0 transition-transform duration-200 ease-out z-40; }
  .sidebar.mobile-open { @apply translate-x-0; }
  .sidebar-header { @apply flex items-center justify-between px-5 py-4 border-b border-slate-200; }
  .sidebar-logo { @apply text-lg font-semibold text-slate-800; }
  .sidebar-nav { @apply px-3 py-4 space-y-6; }
  .nav-section { @apply space-y-2; }
  .nav-section-title { @apply text-xs uppercase tracking-[0.15em] text-slate-400 font-semibold px-3; }
  .nav-item { @apply flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-semibold text-slate-600 hover:bg-primary-50 hover:text-primary-700 cursor-pointer; }
  .nav-item.active { @apply bg-primary-600 text-white shadow-sm; }
  .nav-item .nav-icon { @apply w-5 h-5 text-slate-400 flex items-center justify-center; }
  .nav-item.active .nav-icon { @apply text-white; }
  .nav-text a { @apply block w-full text-inherit no-underline; }
  .sidebar-overlay { @apply fixed inset-0 bg-slate-900/40 z-30 hidden; }
  .sidebar-overlay.active { @apply block; }

  .main-content { @apply flex-1 min-h-screen flex flex-col; }
  .top-bar { @apply sticky top-0 z-20 h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 md:px-6 shadow-sm; }
  .menu-toggle { @apply md:hidden inline-flex items-center justify-center w-10 h-10 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50; }
  .page-title { @apply text-lg font-semibold text-slate-800; }
  .top-bar-actions { @apply flex items-center gap-4; }
  .search-box { @apply hidden md:flex items-center gap-2 bg-slate-100 border border-slate-200 rounded-lg px-3 py-2; }
  .search-input { @apply bg-transparent outline-none text-sm flex-1; }
  .search-icon { @apply text-slate-400; }
  .user-menu { @apply flex items-center gap-3 pl-4 border-l border-slate-200; }
  .user-avatar { @apply w-10 h-10 rounded-full bg-primary-600 text-white flex items-center justify-center font-semibold; }
  .user-info { @apply hidden sm:block; }
  .user-name { @apply text-sm font-semibold; }

  .content-area { @apply p-4 md:p-6 space-y-6; }
  .content-section { @apply space-y-6; }

  .a11y-dashboard { @apply space-y-8; }
  .a11y-hero { @apply bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-6; }
  .a11y-hero-content { @apply flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between; }
  .hero-eyebrow, .dashboard-hero-eyebrow, .forms-hero-eyebrow, .analytics-hero-eyebrow { @apply text-xs font-semibold uppercase tracking-[0.15em] text-primary-600; }
  .a11y-hero-title { @apply text-2xl md:text-3xl font-semibold text-slate-900; }
  .a11y-hero-subtitle { @apply text-slate-600 max-w-3xl; }
  .a11y-hero-actions { @apply flex flex-wrap items-center gap-3; }
  .a11y-hero-meta { @apply text-sm text-slate-500 flex items-center gap-2; }

  .a11y-overview-grid { @apply grid gap-4 md:grid-cols-2 xl:grid-cols-3; }
  .a11y-overview-card { @apply bg-primary-50 border border-primary-100 rounded-xl p-4 flex flex-col gap-2; }
  .a11y-overview-label { @apply text-xs uppercase tracking-[0.12em] text-slate-500; }
  .a11y-overview-value { @apply text-2xl font-bold text-slate-900; }
  .a11y-overview-delta { @apply text-sm font-semibold text-green-600 flex items-center gap-1; }

  .dashboard-panel { @apply bg-white border border-slate-200 rounded-2xl shadow-sm p-5 space-y-4; }
  .dashboard-panel-header { @apply flex flex-col gap-3 md:flex-row md:items-center md:justify-between; }
  .dashboard-panel-title { @apply text-lg font-semibold text-slate-900; }
  .dashboard-panel-description { @apply text-sm text-slate-600; }
  .dashboard-quick-actions, .dashboard-module-card-grid { @apply grid gap-4 md:grid-cols-2 xl:grid-cols-3; }
  .dashboard-quick-card, .dashboard-module-card { @apply border border-slate-200 rounded-xl bg-slate-50 p-4 flex items-start gap-3 shadow-sm; }
  .dashboard-quick-card.placeholder, .dashboard-module-card.placeholder { @apply animate-pulse; }

  .a11y-detail-card { @apply bg-white border border-slate-200 rounded-2xl shadow-sm p-5 space-y-4; }
  .a11y-detail-header { @apply flex flex-col gap-3 md:flex-row md:items-start md:justify-between; }
  .a11y-detail-actions { @apply flex flex-wrap gap-3 items-center; }
  .a11y-detail-content { @apply space-y-4; }
  .a11y-detail-meta { @apply grid grid-cols-1 sm:grid-cols-2 gap-3; }
  .a11y-detail-meta__label { @apply text-sm text-slate-500; }
  .a11y-detail-meta__value { @apply font-semibold text-slate-900; }
  .a11y-detail-grid { @apply grid gap-4 md:grid-cols-2 xl:grid-cols-3; }
  .a11y-empty-state { @apply flex flex-col items-center justify-center text-center gap-3 p-6 border border-dashed border-slate-300 rounded-xl bg-slate-50 text-slate-600; }
  .empty-state { @apply flex flex-col items-center justify-center text-center gap-4 p-6 border border-dashed border-slate-300 rounded-2xl bg-white text-slate-600 shadow-sm; }
  .empty-state__icon { @apply w-12 h-12 rounded-full bg-primary-50 text-primary-600 flex items-center justify-center text-xl; }
  .empty-state__content { @apply space-y-1; }
  .empty-state__title { @apply text-lg font-semibold text-slate-900; }
  .empty-state__description { @apply text-sm text-slate-600 max-w-md; }
  .empty-state__cta { @apply mt-2; }

  .a11y-pages-grid { @apply grid gap-3 sm:grid-cols-2 xl:grid-cols-3; }
  .a11y-page-detail { @apply fixed inset-0 z-40 flex items-start justify-end bg-slate-900/50; }
  .a11y-page-detail .a11y-detail-content { @apply max-w-3xl w-full bg-white h-screen overflow-y-auto p-6 shadow-2xl; }
  .a11y-detail-close { @apply absolute top-4 right-4 inline-flex items-center justify-center w-10 h-10 rounded-full border border-slate-200 text-slate-500 hover:bg-slate-50; }

  .a11y-btn { @apply inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition-colors duration-150; }
  .a11y-btn--primary { @apply bg-primary-600 text-white hover:bg-primary-700 shadow-sm; }
  .a11y-btn--secondary { @apply bg-slate-900 text-white hover:bg-slate-800; }
  .a11y-btn--ghost { @apply bg-white text-slate-700 border border-slate-200 hover:bg-slate-50; }
  .a11y-btn--danger { @apply bg-red-500 text-white hover:bg-red-600; }
  .a11y-btn--icon { @apply p-2 rounded-full; }

  .form-group { @apply space-y-1; }
  .form-label { @apply text-sm font-semibold text-slate-700; }
  .form-input, .form-control { @apply w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500; }
  .form-textarea, textarea.form-control { @apply w-full rounded-lg border border-slate-300 px-3 py-2 text-sm min-h-[140px] focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500; }
  .form-select, select.form-control { @apply w-full rounded-lg border border-slate-300 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500; }
  .form-check { @apply flex items-center gap-2 text-sm text-slate-700; }
  .form-alert, .alert { @apply rounded-lg border border-amber-300 bg-amber-50 px-4 py-3 text-amber-800 text-sm; }

  .badge, .status-badge, .category-tag, .issue-tag, .a11y-issue-tag, .import-status { @apply inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-primary-50 text-primary-700; }
  .status-good, .status-published, .user-status--active, .import-status--success { @apply bg-green-50 text-green-700; }
  .status-warning, .a11y-issue-tag.serious, .import-status--warning { @apply bg-amber-50 text-amber-700; }
  .status-critical, .user-status--inactive, .import-status--error { @apply bg-red-50 text-red-700; }

  .table, .a11y-table-view table { @apply w-full border-collapse; }
  .table th, .a11y-table-header { @apply text-left text-xs font-semibold uppercase tracking-[0.1em] text-slate-500 border-b border-slate-200 pb-2; }
  .table td, .a11y-table-view td { @apply border-b border-slate-100 py-3 text-sm text-slate-700; }
  .a11y-table-view { @apply overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm; }
  .a11y-table-header-row { @apply bg-slate-50; }
  .table-responsive { @apply overflow-x-auto; }

  .content { @apply max-w-5xl mx-auto p-6; }
  .footer { @apply text-center text-sm text-slate-500 py-6; }
  .header { @apply bg-white border-b border-slate-200 px-6 py-4 flex items-center justify-between; }
  .btn { @apply inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold bg-primary-600 text-white hover:bg-primary-700 shadow-sm; }
  .btn.btn-secondary, .btn-secondary { @apply bg-white text-slate-700 border border-slate-200 hover:bg-slate-50; }
  .btn-icon { @apply w-4 h-4; }
  .input-group { @apply flex rounded-lg border border-slate-300 overflow-hidden; }
  .input-group .form-control { @apply border-0 rounded-none flex-1; }
  .input-group .btn { @apply rounded-none px-3; }

  /* Auth screens */
  .login-container { @apply max-w-md mx-auto mt-24 bg-white border border-slate-200 rounded-2xl shadow-lg p-8 space-y-6; }
  .login-container h2 { @apply text-2xl font-semibold text-slate-900 text-center; }
  .login-container label { @apply block text-sm font-semibold text-slate-700 space-y-2; }
  .login-container input { @apply w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500; }
  .login-container .error { @apply rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm; }
  .login-container form { @apply space-y-4; }

  .forms-main-grid { @apply grid gap-4 lg:grid-cols-[2fr,1.3fr]; }
  .forms-library, .forms-submissions-container { @apply space-y-3; }
  .forms-submission-card { @apply rounded-xl border border-slate-200 bg-white shadow-sm p-4 flex flex-col gap-2; }
  .forms-submissions-list { @apply space-y-3; }
  .forms-submissions-empty { @apply text-center text-slate-500 border border-dashed border-slate-300 rounded-xl py-8; }
  .forms-drawer { @apply fixed inset-0 bg-slate-900/50 z-40 flex items-start justify-end; }
  .forms-drawer .a11y-detail-content { @apply w-full max-w-2xl bg-white h-screen overflow-y-auto p-6; }

  .media-grid, .gallery-grid { @apply grid gap-4 sm:grid-cols-2 lg:grid-cols-3; }
  .media-card { @apply rounded-xl border border-slate-200 bg-white shadow-sm p-4 flex flex-col gap-3; }

  .fundraising-tabs { @apply flex flex-wrap gap-4 border-b border-slate-200; }
  .fundraising-tab { @apply px-2 pb-3 text-sm font-semibold text-slate-500 border-b-2 border-transparent transition-colors; }
  .fundraising-tab.is-active { @apply text-primary-600 border-primary-600; }
  .fundraising-tabpanel { @apply space-y-6; }

  .search-box input::placeholder { @apply text-slate-400; }
}
</style>
