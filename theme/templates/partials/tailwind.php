<?php
// Tailwind CDN helpers for public theme pages.
?>
<script>
window.tailwind = window.tailwind || {};
tailwind.config = {
    theme: {
        extend: {
            colors: {
                primary: {
                    50: '#eff6ff',
                    100: '#dbeafe',
                    200: '#bfdbfe',
                    300: '#93c5fd',
                    400: '#60a5fa',
                    500: '#3b82f6',
                    600: '#2563eb',
                    700: '#1d4ed8',
                    800: '#1e40af',
                    900: '#1e3a8a'
                },
                secondary: {
                    50: '#f8fafc',
                    100: '#f1f5f9',
                    200: '#e2e8f0',
                    300: '#cbd5e1',
                    400: '#94a3b8',
                    500: '#64748b',
                    600: '#475569',
                    700: '#334155',
                    800: '#1f2937',
                    900: '#0f172a'
                },
                accent: {
                    500: '#14b8a6',
                    600: '#0d9488'
                }
            },
            fontFamily: {
                sans: ['Poppins', 'Inter', 'system-ui', 'sans-serif'],
                serif: ['PT Serif', 'Georgia', 'serif']
            },
            boxShadow: {
                soft: '0 18px 40px -24px rgba(15, 23, 42, 0.45)'
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
  :root {
    --primary: #2563eb;
    --secondary: #0f172a;
    --third: #14b8a6;
    --fourth: #f97316;
    --white: #ffffff;
    --black: #0b1120;
    --light: #f1f5f9;
    --gray: #94a3b8;
  }

  body {
    @apply bg-slate-50 text-slate-900 font-sans antialiased;
  }

  h1 {
    @apply text-4xl md:text-5xl font-semibold tracking-tight text-slate-900;
  }

  h2 {
    @apply text-3xl md:text-4xl font-semibold tracking-tight text-slate-900;
  }

  h3 {
    @apply text-2xl md:text-3xl font-semibold text-slate-900;
  }

  h4 {
    @apply text-xl md:text-2xl font-semibold text-slate-900;
  }

  h5 {
    @apply text-lg font-semibold text-slate-900;
  }

  h6 {
    @apply text-base font-semibold text-slate-900;
  }

  p {
    @apply text-base text-slate-600;
  }

  a {
    @apply text-primary-600 hover:text-primary-700 transition-colors;
  }

  small {
    @apply text-sm text-slate-500;
  }
}

@layer components {
  .section {
    @apply py-16 md:py-20;
  }

  .container {
    @apply mx-auto w-full max-w-6xl px-4;
  }

  .container-fluid {
    @apply w-full px-4;
  }

  .btn {
    @apply inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold transition;
  }

  .card {
    @apply rounded-2xl border border-slate-200 bg-white shadow-sm;
  }

  .card-body {
    @apply p-6;
  }

  .badge {
    @apply inline-flex items-center rounded-full bg-primary-50 px-3 py-1 text-xs font-semibold text-primary-700;
  }

  .btn-primary {
    @apply btn bg-primary-600 text-white shadow-sm hover:bg-primary-700;
  }

  .btn-secondary {
    @apply btn bg-slate-900 text-white shadow-sm hover:bg-slate-800;
  }

  .btn-outline {
    @apply btn border border-slate-200 bg-white text-slate-700 shadow-sm hover:bg-slate-50;
  }

  .btn-outline-primary {
    @apply btn border border-primary-200 bg-primary-50 text-primary-700 shadow-sm hover:bg-primary-100;
  }

  .btn-outline-secondary {
    @apply btn border border-slate-200 bg-white text-slate-700 shadow-sm hover:bg-slate-50;
  }

  .btn-outline-light {
    @apply btn border border-white/40 bg-white/10 text-white shadow-sm hover:bg-white/20;
  }

  .btn-sm {
    @apply px-3 py-1.5 text-xs;
  }

  .btn-icon {
    @apply h-4 w-4;
  }

  .btn-label {
    @apply whitespace-nowrap;
  }

  .row {
    @apply flex flex-wrap -mx-4;
  }

  .col {
    @apply w-full px-4;
  }

  .col-md-4 {
    @apply w-full px-4 md:w-1/3;
  }

  .col-md-6 {
    @apply w-full px-4 md:w-1/2;
  }

  .col-md-8 {
    @apply w-full px-4 md:w-2/3;
  }

  .col-lg-4 {
    @apply w-full px-4 lg:w-1/3;
  }

  .col-lg-5 {
    @apply w-full px-4 lg:w-5/12;
  }

  .col-lg-8 {
    @apply w-full px-4 lg:w-2/3;
  }

  .col-lg-9 {
    @apply w-full px-4 lg:w-3/4;
  }

  .col-xl-3 {
    @apply w-full px-4 xl:w-1/4;
  }

  .g-0 {
    @apply gap-0;
  }

  .g-1 {
    @apply gap-2;
  }

  .g-3 {
    @apply gap-6;
  }

  .g-4 {
    @apply gap-8;
  }

  .g-5 {
    @apply gap-10;
  }

  .input {
    @apply w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200;
  }

  .textarea {
    @apply w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200;
  }

  .select {
    @apply w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200;
  }

  .drop-area {
    @apply min-h-[96px] rounded-2xl border border-dashed border-slate-200 bg-white/60 p-4;
  }

  .form-control {
    @apply w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200;
  }

  .form-select {
    @apply w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200;
  }

  .form-text {
    @apply text-sm text-slate-500;
  }

  .formGroup {
    @apply text-xs font-semibold uppercase tracking-wide text-slate-500;
  }

  .input-group {
    @apply flex items-center rounded-lg border border-slate-300 bg-white shadow-sm;
  }

  .input-group .form-control {
    @apply border-0 bg-transparent shadow-none focus:ring-0;
  }

  .input-group .btn {
    @apply rounded-none;
  }

  .text-muted {
    @apply text-slate-500;
  }

  .text-primary {
    @apply text-primary-600;
  }

  .bg-light {
    @apply bg-slate-100;
  }

  .bg-primary {
    @apply bg-primary-600 text-white;
  }

  .bg-secondary {
    @apply bg-slate-900 text-white;
  }

  .bg-success {
    @apply bg-emerald-500 text-white;
  }

  .bg-info {
    @apply bg-sky-500 text-white;
  }

  .bg-dark {
    @apply bg-slate-900 text-white;
  }

  .border-0 {
    @apply border-0;
  }

  .border-top {
    @apply border-t border-slate-200;
  }

  .border-bottom {
    @apply border-b border-slate-200;
  }

  .d-flex {
    @apply flex;
  }

  .align-items-center {
    @apply items-center;
  }

  .align-items-end {
    @apply items-end;
  }

  .order-lg-first {
    @apply lg:order-first;
  }

  .order-lg-last {
    @apply lg:order-last;
  }

  .justify-between {
    @apply justify-between;
  }

  .text-center {
    @apply text-center;
  }

  .text-start {
    @apply text-left;
  }

  .text-end {
    @apply text-right;
  }

  .d-none {
    @apply hidden;
  }

  .list-unstyled {
    @apply list-none p-0 m-0;
  }

  .w-100 {
    @apply w-full;
  }

  .me-auto {
    @apply mr-auto;
  }

  .ms-auto {
    @apply ml-auto;
  }

  .me-2 {
    @apply mr-2;
  }

  .ms-2 {
    @apply ml-2;
  }

  .content-width-480 { @apply max-w-[480px]; }
  .content-width-570 { @apply max-w-[570px]; }
  .content-width-670 { @apply max-w-[670px]; }
  .content-width-770 { @apply max-w-[770px]; }
  .content-width-870 { @apply max-w-[870px]; }
  .content-width-970 { @apply max-w-[970px]; }
  .content-width-1170 { @apply max-w-[1170px]; }
  .content-width-1240 { @apply max-w-[1240px]; }

  .layout-20-80,
  .layout-30-70,
  .layout-40-60,
  .layout-50-50,
  .layout-60-40,
  .layout-70-30,
  .layout-80-20 {
    @apply flex flex-wrap;
  }

  .layout-20-80 > .col,
  .layout-30-70 > .col,
  .layout-40-60 > .col,
  .layout-50-50 > .col,
  .layout-60-40 > .col,
  .layout-70-30 > .col,
  .layout-80-20 > .col {
    @apply w-full;
  }

  @media (min-width: 768px) {
    .layout-20-80 > .col:first-child { width: 20%; }
    .layout-20-80 > .col:last-child { width: 80%; }
    .layout-30-70 > .col:first-child { width: 30%; }
    .layout-30-70 > .col:last-child { width: 70%; }
    .layout-40-60 > .col:first-child { width: 40%; }
    .layout-40-60 > .col:last-child { width: 60%; }
    .layout-50-50 > .col:first-child,
    .layout-50-50 > .col:last-child { width: 50%; }
    .layout-60-40 > .col:first-child { width: 60%; }
    .layout-60-40 > .col:last-child { width: 40%; }
    .layout-70-30 > .col:first-child { width: 70%; }
    .layout-70-30 > .col:last-child { width: 30%; }
    .layout-80-20 > .col:first-child { width: 80%; }
    .layout-80-20 > .col:last-child { width: 20%; }
  }

  .h4 {
    @apply text-xl font-semibold text-slate-900;
  }

  .h5 {
    @apply text-lg font-semibold text-slate-900;
  }

  .sidebar-inner {
    @apply rounded-2xl border border-slate-200 bg-white p-6 shadow-sm;
  }

  .display-5 {
    @apply text-4xl md:text-5xl font-semibold tracking-tight text-slate-900;
  }

  .display-6 {
    @apply text-3xl md:text-4xl font-semibold tracking-tight text-slate-900;
  }

  .lead {
    @apply text-lg text-slate-600;
  }

  .progress {
    @apply h-2 w-full overflow-hidden rounded-full bg-slate-200;
  }

  .progress-bar {
    @apply h-full rounded-full bg-primary-600;
  }

  .mw-rich-text {
    @apply space-y-4 text-slate-600;
  }

  .mw-rich-text h2 {
    @apply text-2xl font-semibold text-slate-900;
  }

  .mw-rich-text h3 {
    @apply text-xl font-semibold text-slate-900;
  }

  .mw-rich-text ul {
    @apply list-disc pl-5;
  }

  .mw-rich-text a {
    @apply text-primary-600 hover:text-primary-700;
  }

  .hero-section {
    @apply relative isolate overflow-hidden rounded-3xl bg-slate-900 text-white shadow-soft;
  }

  .hero-overlay {
    @apply absolute inset-0 bg-slate-900/70;
  }

  .hero-content {
    @apply relative z-10;
  }

  .hero-small {
    @apply py-14 md:py-20;
  }

  .hero-medium {
    @apply py-20 md:py-28;
  }

  .hero-large {
    @apply py-24 md:py-36;
  }

  .hero-side {
    @apply py-16 md:py-24;
  }

  .cta-banner {
    @apply relative overflow-hidden rounded-3xl bg-cover bg-center;
  }

  .cta-banner__inner {
    @apply relative z-10 mx-auto w-full max-w-4xl space-y-4 py-12 md:py-16;
  }

  .cta-banner__headline {
    @apply text-3xl md:text-4xl font-semibold tracking-tight text-slate-900;
  }

  .cta-banner__subcopy {
    @apply text-lg text-slate-600;
  }

  .cta-banner__actions {
    @apply mt-6 flex flex-wrap gap-3;
  }

  .cta-banner--text-light .cta-banner__headline,
  .cta-banner--text-light .cta-banner__subcopy {
    @apply text-white;
  }

  .cta-banner--text-light .cta-banner__subcopy {
    @apply text-slate-200;
  }

  .cta-banner--text-dark .cta-banner__headline {
    @apply text-slate-900;
  }

  .cta-banner--text-dark .cta-banner__subcopy {
    @apply text-slate-600;
  }

  .divider-solid {
    @apply border-t border-slate-200;
  }

  .divider-dashed {
    @apply border-t border-dashed border-slate-200;
  }

  .divider-dotted {
    @apply border-t border-dotted border-slate-200;
  }

  .map-block__body {
    @apply grid gap-6 lg:grid-cols-[0.45fr_0.55fr];
  }

  .map-block__panel {
    @apply rounded-2xl border border-slate-200 bg-white p-6 shadow-sm;
  }

  .map-block__filters {
    @apply flex flex-wrap gap-2 pb-4;
  }

  .map-block__list {
    @apply space-y-4;
  }

  .map-block__item {
    @apply rounded-xl border border-slate-200 bg-slate-50 p-4;
  }

  .map-block__item-title {
    @apply text-lg font-semibold text-slate-900;
  }

  .map-block__item-meta {
    @apply text-sm text-slate-500;
  }

  .map-block__map {
    @apply relative overflow-hidden rounded-2xl border border-slate-200 bg-slate-100;
  }

  .map-block__loading {
    @apply absolute inset-0 flex items-center justify-center text-sm text-slate-500;
  }

  [data-map-height=\"short\"] .map-block__map {
    @apply h-[320px];
  }

  [data-map-height=\"medium\"] .map-block__map {
    @apply h-[420px];
  }

  [data-map-height=\"tall\"] .map-block__map {
    @apply h-[540px];
  }

  .spark-form-embed {
    @apply rounded-2xl border border-slate-200 bg-white p-6 shadow-sm;
  }

  .spark-form-placeholder {
    @apply text-sm text-slate-500;
  }

  .rich-text {
    @apply space-y-4 text-slate-600;
  }

  .rich-text h2 {
    @apply text-2xl font-semibold text-slate-900;
  }

  .rich-text h3 {
    @apply text-xl font-semibold text-slate-900;
  }

  .rich-text ul {
    @apply list-disc pl-5;
  }

  .rich-text a {
    @apply text-primary-600 hover:text-primary-700;
  }

  .blog-post-list .blog-posts {
    @apply grid gap-6 md:grid-cols-2 xl:grid-cols-3;
  }

  .blog-item {
    @apply flex h-full flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm;
  }

  .blog-item--placeholder,
  .blog-item--error {
    @apply text-slate-500 italic;
  }

  .blog-title {
    @apply text-xl font-semibold text-slate-900;
  }

  .blog-meta {
    @apply text-sm text-slate-500 flex flex-wrap gap-3;
  }

  .blog-excerpt {
    @apply text-slate-600;
  }

  .blog-read-more {
    @apply text-sm font-semibold text-primary-600 hover:text-primary-700;
  }

  .blog-post-detail__back-link {
    @apply inline-flex items-center gap-2 text-sm font-semibold text-primary-600 hover:text-primary-700;
  }

  .blog-post-detail__category {
    @apply inline-flex items-center rounded-full bg-primary-50 px-3 py-1 text-xs font-semibold text-primary-700;
  }

  .blog-post-detail__title {
    @apply text-4xl md:text-5xl font-semibold tracking-tight text-slate-900;
  }

  .blog-post-detail__meta {
    @apply flex flex-wrap gap-4 text-sm text-slate-500;
  }

  .blog-post-detail__image {
    @apply w-full rounded-2xl object-cover;
  }

  .blog-post-detail__tag {
    @apply inline-flex items-center rounded-full border border-primary-200 bg-primary-50 px-3 py-1 text-xs font-semibold text-primary-700;
  }

  .events-block__items {
    @apply grid gap-6 md:grid-cols-2 xl:grid-cols-3;
  }

  .events-block--layout-list .events-block__items {
    @apply space-y-4 md:space-y-6;
  }

  .events-block--layout-list .events-block__item {
    @apply flex flex-col gap-4 md:flex-row md:items-start;
  }

  .events-block--layout-compact .events-block__items {
    @apply divide-y divide-slate-200;
  }

  .events-block--layout-compact .events-block__item {
    @apply rounded-none border-0 border-b border-slate-200 py-5 shadow-none;
  }

  .events-block__item {
    @apply flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm;
  }

  .events-block__item--placeholder {
    @apply text-slate-500 italic;
  }

  .events-block__title {
    @apply text-xl font-semibold text-slate-900;
  }

  .events-block__date {
    @apply inline-flex flex-col items-center justify-center rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700;
  }

  .events-block__date-month {
    @apply uppercase tracking-widest text-[10px] text-slate-500;
  }

  .events-block__date-day {
    @apply text-lg font-semibold text-slate-900;
  }

  .events-block__price {
    @apply text-sm font-semibold text-slate-700;
  }

  .events-block__location,
  .events-block__time {
    @apply text-sm text-slate-500;
  }

  .events-block__meta {
    @apply space-y-1 text-sm text-slate-500;
  }

  .events-block__meta-line {
    @apply flex flex-wrap items-center gap-2;
  }

  .events-block__badge {
    @apply inline-flex items-center rounded-full bg-primary-50 px-3 py-1 text-xs font-semibold text-primary-700;
  }

  .events-block__description {
    @apply text-slate-600;
  }

  .events-block__cta {
    @apply inline-flex items-center gap-2 text-sm font-semibold text-primary-600 hover:text-primary-700;
  }

  .events-block__image {
    @apply h-40 w-full rounded-xl object-cover;
  }

  .events-block__media-fallback {
    @apply flex h-40 items-center justify-center rounded-xl bg-slate-100 text-slate-400;
  }
}
</style>
