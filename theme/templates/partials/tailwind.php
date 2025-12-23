<?php
// Tailwind CDN helpers for public theme pages.
?>
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
                serif: ['PT Serif', 'Georgia', 'serif']
            }
        }
    }
};
</script>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=PT+Serif:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<style type="text/tailwindcss">
@layer base {
  body { @apply bg-slate-50 text-slate-900 font-sans antialiased; }
  h1,h2,h3,h4,h5,h6 { @apply font-semibold text-slate-900; }
  a { @apply text-primary-600 hover:text-primary-700; }
}

@layer components {
  .container { @apply w-full max-w-6xl mx-auto px-4; }
  .row { @apply flex flex-wrap -mx-3; }
  .col { @apply w-full px-3; }
  .col-md-4 { @apply px-3 w-full md:w-1/3; }
  .col-md-6 { @apply px-3 w-full md:w-1/2; }
  .col-md-8 { @apply px-3 w-full md:w-2/3; }
  .col-lg-4 { @apply px-3 w-full lg:w-1/3; }
  .col-lg-5 { @apply px-3 w-full lg:w-5/12; }
  .col-lg-8 { @apply px-3 w-full lg:w-2/3; }
  .col-lg-9 { @apply px-3 w-full lg:w-3/4; }
  .col-xl-3 { @apply px-3 w-full xl:w-1/4; }

  .g-0 { @apply gap-0; }
  .g-1 { @apply gap-1; }
  .g-3 { @apply gap-3; }
  .g-4 { @apply gap-4; }
  .g-5 { @apply gap-5; }

  .navbar { @apply flex items-center justify-between py-4; }
  .navbar-brand { @apply inline-flex items-center font-semibold text-lg text-slate-900 no-underline; }
  .navbar-nav { @apply flex flex-col md:flex-row md:items-center gap-2 md:gap-4; }
  .nav-link { @apply text-slate-700 hover:text-primary-700 text-sm font-medium no-underline; }
  .nav-link.active { @apply text-primary-700; }
  .dropdown-menu { @apply mt-2 rounded-lg border border-slate-200 bg-white shadow-lg p-2 space-y-1; }
  .dropdown .dropdown-menu { @apply hidden md:block; }

  .btn { @apply inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold shadow-sm; }
  .btn-primary { @apply bg-primary-600 text-white hover:bg-primary-700; }
  .btn-outline-primary { @apply border border-primary-200 text-primary-700 bg-primary-50 hover:bg-primary-100; }
  .btn-outline-secondary { @apply border border-slate-200 text-slate-700 bg-white hover:bg-slate-50; }
  .btn-outline-light { @apply border border-white/40 text-white bg-white/10 hover:bg-white/20; }
  .btn-secondary { @apply bg-slate-900 text-white hover:bg-slate-800; }
  .btn-sm { @apply text-xs px-3 py-1.5; }

  .card { @apply bg-white rounded-xl border border-slate-200 shadow-sm; }
  .card-body { @apply p-6; }
  .shadow-sm { @apply shadow-sm; }
  .border-0 { @apply border-0; }
  .border-bottom { @apply border-b border-slate-200; }
  .border-top { @apply border-t border-slate-200; }

  .form-control { @apply w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500; }
  .input-group { @apply flex rounded-lg border border-slate-300 overflow-hidden; }
  .input-group .form-control { @apply border-0 rounded-none flex-1; }
  .input-group .btn { @apply rounded-none; }

  .badge { @apply inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-primary-50 text-primary-700; }
  .bg-light { @apply bg-slate-100; }
  .text-muted { @apply text-slate-500; }
  .text-primary { @apply text-primary-600; }
  .text-white { @apply text-white; }
  .text-dark { @apply text-slate-900; }
  .text-center { @apply text-center; }
  .lead { @apply text-lg text-slate-600; }
  .display-5 { @apply text-4xl md:text-5xl font-semibold; }

  .filter-invert { filter: invert(1); }
  .drop-area { @apply min-h-[60px] border border-dashed border-slate-200 rounded-lg; }
  .page-template { @apply min-h-screen flex flex-col; }
  .site-footer { @apply bg-slate-900 text-slate-100; }
}

@layer utilities {
  .btn-icon { @apply w-4 h-4; }
  .btn-label { @apply whitespace-nowrap; }
}
</style>
