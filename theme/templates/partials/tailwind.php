<?php
// Shared theme styles without Tailwind runtime.
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=PT+Serif:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<style>
:root {
  --primary-50: #eff6ff;
  --primary-100: #dbeafe;
  --primary-200: #bfdbfe;
  --primary-300: #93c5fd;
  --primary-400: #60a5fa;
  --primary-500: #3b82f6;
  --primary-600: #2563eb;
  --primary-700: #1d4ed8;
  --primary-800: #1e40af;
  --primary-900: #1e3a8a;
  --secondary-50: #f8fafc;
  --secondary-100: #f1f5f9;
  --secondary-200: #e2e8f0;
  --secondary-300: #cbd5e1;
  --secondary-400: #94a3b8;
  --secondary-500: #64748b;
  --secondary-600: #475569;
  --secondary-700: #334155;
  --secondary-800: #1f2937;
  --secondary-900: #0f172a;
  --accent-500: #14b8a6;
  --accent-600: #0d9488;
  --slate-50: #f8fafc;
  --slate-100: #f1f5f9;
  --slate-200: #e2e8f0;
  --slate-300: #cbd5e1;
  --slate-400: #94a3b8;
  --slate-500: #64748b;
  --slate-600: #475569;
  --slate-700: #334155;
  --slate-800: #1e293b;
  --slate-900: #0f172a;
  --emerald-500: #10b981;
  --sky-500: #0ea5e9;
  --red-500: #ef4444;
  --white: #ffffff;
}

* {
  box-sizing: border-box;
}

body {
  background-color: var(--slate-50);
  color: var(--slate-900);
  font-family: 'Poppins', 'Inter', system-ui, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

h1 {
  font-size: 2.25rem;
  font-weight: 600;
  letter-spacing: -0.025em;
  color: var(--slate-900);
}

h2 {
  font-size: 1.875rem;
  font-weight: 600;
  letter-spacing: -0.025em;
  color: var(--slate-900);
}

h3 {
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--slate-900);
}

h4 {
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--slate-900);
}

h5 {
  font-size: 1.125rem;
  font-weight: 600;
  color: var(--slate-900);
}

h6 {
  font-size: 1rem;
  font-weight: 600;
  color: var(--slate-900);
}

p {
  font-size: 1rem;
  color: var(--slate-600);
}

a {
  color: var(--primary-600);
  transition: color 0.2s ease;
}

a:hover {
  color: var(--primary-700);
}

small {
  font-size: 0.875rem;
  color: var(--slate-500);
}

.section {
  padding: 4rem 0;
}

.container {
  margin: 0 auto;
  width: 100%;
  max-width: 72rem;
  padding: 0 1rem;
}

.container-fluid {
  width: 100%;
  padding: 0 1rem;
}

.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  border-radius: 0.5rem;
  padding: 0.5rem 1rem;
  font-size: 0.875rem;
  font-weight: 600;
  transition: all 0.2s ease;
}

.card {
  border-radius: 1rem;
  border: 1px solid var(--slate-200);
  background-color: var(--white);
  box-shadow: 0 1px 2px 0 rgba(15, 23, 42, 0.08);
}

.card-body {
  padding: 1.5rem;
}

.badge {
  display: inline-flex;
  align-items: center;
  border-radius: 9999px;
  background-color: var(--primary-50);
  padding: 0.25rem 0.75rem;
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--primary-700);
}

.btn-primary {
  background-color: var(--primary-600);
  color: var(--white);
  box-shadow: 0 1px 2px 0 rgba(15, 23, 42, 0.08);
}

.btn-primary:hover {
  background-color: var(--primary-700);
}

.btn-secondary {
  background-color: var(--slate-900);
  color: var(--white);
  box-shadow: 0 1px 2px 0 rgba(15, 23, 42, 0.08);
}

.btn-secondary:hover {
  background-color: var(--slate-800);
}

.btn-outline {
  border: 1px solid var(--slate-200);
  background-color: var(--white);
  color: var(--slate-700);
  box-shadow: 0 1px 2px 0 rgba(15, 23, 42, 0.08);
}

.btn-outline:hover {
  background-color: var(--slate-50);
}

.btn-outline-primary {
  border: 1px solid var(--primary-200);
  background-color: var(--primary-50);
  color: var(--primary-700);
  box-shadow: 0 1px 2px 0 rgba(15, 23, 42, 0.08);
}

.btn-outline-primary:hover {
  background-color: var(--primary-100);
}

.btn-outline-secondary {
  border: 1px solid var(--slate-200);
  background-color: var(--white);
  color: var(--slate-700);
  box-shadow: 0 1px 2px 0 rgba(15, 23, 42, 0.08);
}

.btn-outline-secondary:hover {
  background-color: var(--slate-50);
}

.btn-outline-light {
  border: 1px solid rgba(255, 255, 255, 0.4);
  background-color: rgba(255, 255, 255, 0.1);
  color: var(--white);
  box-shadow: 0 1px 2px 0 rgba(15, 23, 42, 0.08);
}

.btn-outline-light:hover {
  background-color: rgba(255, 255, 255, 0.2);
}

.btn-sm {
  padding: 0.375rem 0.75rem;
  font-size: 0.75rem;
}

.btn-icon {
  width: 1rem;
  height: 1rem;
}

.btn-label {
  white-space: nowrap;
}

.row {
  display: flex;
  flex-wrap: wrap;
  margin-left: -1rem;
  margin-right: -1rem;
}

.col {
  width: 100%;
  padding: 0 1rem;
}

.col-md-4,
.col-md-6,
.col-md-8,
.col-lg-4,
.col-lg-5,
.col-lg-8,
.col-lg-9,
.col-xl-3 {
  width: 100%;
  padding: 0 1rem;
}

.g-0 {
  gap: 0;
}

.g-1 {
  gap: 0.5rem;
}

.g-3 {
  gap: 1.5rem;
}

.g-4 {
  gap: 2rem;
}

.g-5 {
  gap: 2.5rem;
}

.input,
.textarea,
.select,
.form-control,
.form-select {
  width: 100%;
  border-radius: 0.5rem;
  border: 1px solid var(--slate-300);
  background-color: var(--white);
  padding: 0.5rem 0.75rem;
  font-size: 0.875rem;
  color: var(--slate-900);
  box-shadow: 0 1px 2px 0 rgba(15, 23, 42, 0.05);
}

.input:focus,
.textarea:focus,
.select:focus,
.form-control:focus,
.form-select:focus {
  border-color: var(--primary-500);
  outline: none;
  box-shadow: 0 0 0 2px var(--primary-200);
}

.drop-area {
  min-height: 96px;
  border-radius: 1rem;
  border: 1px dashed var(--slate-200);
  background-color: rgba(255, 255, 255, 0.6);
  padding: 1rem;
}

.form-text {
  font-size: 0.875rem;
  color: var(--slate-500);
}

.formGroup {
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--slate-500);
}

.input-group {
  display: flex;
  align-items: center;
  border-radius: 0.5rem;
  border: 1px solid var(--slate-300);
  background-color: var(--white);
  box-shadow: 0 1px 2px 0 rgba(15, 23, 42, 0.05);
}

.input-group .form-control {
  border: 0;
  background-color: transparent;
  box-shadow: none;
}

.input-group .btn {
  border-radius: 0;
}

.text-muted {
  color: var(--slate-500);
}

.text-primary {
  color: var(--primary-600);
}

.bg-light {
  background-color: var(--slate-100);
}

.bg-primary {
  background-color: var(--primary-600);
  color: var(--white);
}

.bg-secondary {
  background-color: var(--slate-900);
  color: var(--white);
}

.bg-success {
  background-color: var(--emerald-500);
  color: var(--white);
}

.bg-info {
  background-color: var(--sky-500);
  color: var(--white);
}

.bg-dark {
  background-color: var(--slate-900);
  color: var(--white);
}

.border-0 {
  border: 0;
}

.border-top {
  border-top: 1px solid var(--slate-200);
}

.border-bottom {
  border-bottom: 1px solid var(--slate-200);
}

.d-flex {
  display: flex;
}

.align-items-center {
  align-items: center;
}

.align-items-end {
  align-items: flex-end;
}

.justify-between {
  justify-content: space-between;
}

.text-center {
  text-align: center;
}

.text-start {
  text-align: left;
}

.text-end {
  text-align: right;
}

.d-none {
  display: none;
}

.list-unstyled {
  list-style: none;
  padding: 0;
  margin: 0;
}

.w-100 {
  width: 100%;
}

.me-auto {
  margin-right: auto;
}

.ms-auto {
  margin-left: auto;
}

.me-2 {
  margin-right: 0.5rem;
}

.ms-2 {
  margin-left: 0.5rem;
}

.content-width-480 { max-width: 480px; }
.content-width-570 { max-width: 570px; }
.content-width-670 { max-width: 670px; }
.content-width-770 { max-width: 770px; }
.content-width-870 { max-width: 870px; }
.content-width-970 { max-width: 970px; }
.content-width-1170 { max-width: 1170px; }
.content-width-1240 { max-width: 1240px; }

.layout-20-80,
.layout-30-70,
.layout-40-60,
.layout-50-50,
.layout-60-40,
.layout-70-30,
.layout-80-20 {
  display: flex;
  flex-wrap: wrap;
}

.layout-20-80 > .col,
.layout-30-70 > .col,
.layout-40-60 > .col,
.layout-50-50 > .col,
.layout-60-40 > .col,
.layout-70-30 > .col,
.layout-80-20 > .col {
  width: 100%;
}

.h4 {
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--slate-900);
}

.h5 {
  font-size: 1.125rem;
  font-weight: 600;
  color: var(--slate-900);
}

.sidebar-inner {
  border-radius: 1rem;
  border: 1px solid var(--slate-200);
  background-color: var(--white);
  padding: 1.5rem;
  box-shadow: 0 1px 2px 0 rgba(15, 23, 42, 0.08);
}

.display-5 {
  font-size: 2.25rem;
  font-weight: 600;
  letter-spacing: -0.025em;
  color: var(--slate-900);
}

.display-6 {
  font-size: 1.875rem;
  font-weight: 600;
  letter-spacing: -0.025em;
  color: var(--slate-900);
}

.lead {
  font-size: 1.125rem;
  color: var(--slate-600);
}

.progress {
  height: 0.5rem;
  width: 100%;
  overflow: hidden;
  border-radius: 9999px;
  background-color: var(--slate-200);
}

.progress-bar {
  height: 100%;
  border-radius: 9999px;
  background-color: var(--primary-600);
}

.mw-rich-text {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  color: var(--slate-600);
}

.mw-rich-text h2 {
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--slate-900);
}

.mw-rich-text h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--slate-900);
}

.mw-rich-text ul {
  list-style: disc;
  padding-left: 1.25rem;
}

.mw-rich-text a {
  color: var(--primary-600);
}

.mw-rich-text a:hover {
  color: var(--primary-700);
}

.hero-section {
  position: relative;
  isolation: isolate;
  overflow: hidden;
  border-radius: 1.5rem;
  background-color: var(--slate-900);
  color: var(--white);
  box-shadow: 0 18px 40px -24px rgba(15, 23, 42, 0.45);
}

.hero-overlay {
  position: absolute;
  inset: 0;
  background-color: rgba(15, 23, 42, 0.7);
}

.hero-content {
  position: relative;
  z-index: 10;
}

.hero-small {
  padding: 3.5rem 0;
}

.hero-medium {
  padding: 5rem 0;
}

.hero-large {
  padding: 6rem 0;
}

.hero-side {
  padding: 4rem 0;
}

.cta-banner {
  position: relative;
  overflow: hidden;
  border-radius: 1.5rem;
  background-size: cover;
  background-position: center;
}

.cta-banner__inner {
  position: relative;
  z-index: 10;
  margin: 0 auto;
  width: 100%;
  max-width: 56rem;
  padding: 3rem 0;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.cta-banner__headline {
  font-size: 1.875rem;
  font-weight: 600;
  letter-spacing: -0.025em;
  color: var(--slate-900);
}

.cta-banner__subcopy {
  font-size: 1.125rem;
  color: var(--slate-600);
}

.cta-banner__actions {
  margin-top: 1.5rem;
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.cta-banner--text-light .cta-banner__headline,
.cta-banner--text-light .cta-banner__subcopy {
  color: var(--white);
}

.cta-banner--text-light .cta-banner__subcopy {
  color: var(--slate-200);
}

.cta-banner--text-dark .cta-banner__headline {
  color: var(--slate-900);
}

.cta-banner--text-dark .cta-banner__subcopy {
  color: var(--slate-600);
}

.divider-solid {
  border-top: 1px solid var(--slate-200);
}

.divider-dashed {
  border-top: 1px dashed var(--slate-200);
}

.divider-dotted {
  border-top: 1px dotted var(--slate-200);
}

.map-block__body {
  display: grid;
  gap: 1.5rem;
}

.map-block__panel {
  border-radius: 1rem;
  border: 1px solid var(--slate-200);
  background-color: var(--white);
  padding: 1.5rem;
  box-shadow: 0 1px 2px 0 rgba(15, 23, 42, 0.08);
}

.map-block__filters {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  padding-bottom: 1rem;
}

.map-block__list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.map-block__item {
  border-radius: 0.75rem;
  border: 1px solid var(--slate-200);
  background-color: var(--slate-50);
  padding: 1rem;
}

.map-block__item-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: var(--slate-900);
}

.map-block__item-meta {
  font-size: 0.875rem;
  color: var(--slate-500);
}

.map-block__map {
  position: relative;
  overflow: hidden;
  border-radius: 1rem;
  border: 1px solid var(--slate-200);
  background-color: var(--slate-100);
}

.map-block__loading {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.875rem;
  color: var(--slate-500);
}

[data-map-height="short"] .map-block__map {
  height: 320px;
}

[data-map-height="medium"] .map-block__map {
  height: 420px;
}

[data-map-height="tall"] .map-block__map {
  height: 540px;
}

.spark-form-embed {
  border-radius: 1rem;
  border: 1px solid var(--slate-200);
  background-color: var(--white);
  padding: 1.5rem;
  box-shadow: 0 1px 2px 0 rgba(15, 23, 42, 0.08);
}

.spark-form-placeholder {
  font-size: 0.875rem;
  color: var(--slate-500);
}

.rich-text {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  color: var(--slate-600);
}

.rich-text h2 {
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--slate-900);
}

.rich-text h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--slate-900);
}

.rich-text ul {
  list-style: disc;
  padding-left: 1.25rem;
}

.rich-text a {
  color: var(--primary-600);
}

.rich-text a:hover {
  color: var(--primary-700);
}

.blog-post-list .blog-posts {
  display: grid;
  gap: 1.5rem;
}

.blog-item {
  display: flex;
  height: 100%;
  flex-direction: column;
  gap: 0.75rem;
  border-radius: 1rem;
  border: 1px solid var(--slate-200);
  background-color: var(--white);
  padding: 1.5rem;
  box-shadow: 0 1px 2px 0 rgba(15, 23, 42, 0.08);
}

.blog-item--placeholder,
.blog-item--error {
  color: var(--slate-500);
  font-style: italic;
}

.blog-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--slate-900);
}

.blog-meta {
  font-size: 0.875rem;
  color: var(--slate-500);
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.blog-excerpt {
  color: var(--slate-600);
}

.blog-read-more {
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--primary-600);
}

.blog-read-more:hover {
  color: var(--primary-700);
}

.blog-post-detail__back-link {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--primary-600);
}

.blog-post-detail__back-link:hover {
  color: var(--primary-700);
}

.blog-post-detail__category {
  display: inline-flex;
  align-items: center;
  border-radius: 9999px;
  background-color: var(--primary-50);
  padding: 0.25rem 0.75rem;
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--primary-700);
}

.blog-post-detail__title {
  font-size: 2.25rem;
  font-weight: 600;
  letter-spacing: -0.025em;
  color: var(--slate-900);
}

.blog-post-detail__meta {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  font-size: 0.875rem;
  color: var(--slate-500);
}

.blog-post-detail__image {
  width: 100%;
  border-radius: 1rem;
  object-fit: cover;
}

.blog-post-detail__tag {
  display: inline-flex;
  align-items: center;
  border-radius: 9999px;
  border: 1px solid var(--primary-200);
  background-color: var(--primary-50);
  padding: 0.25rem 0.75rem;
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--primary-700);
}

.events-block__items {
  display: grid;
  gap: 1.5rem;
}

.events-block--layout-list .events-block__items {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.events-block--layout-list .events-block__item {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.events-block--layout-compact .events-block__items {
  display: flex;
  flex-direction: column;
  gap: 0;
  border-top: 1px solid var(--slate-200);
}

.events-block--layout-compact .events-block__item {
  border-radius: 0;
  border: 0;
  border-bottom: 1px solid var(--slate-200);
  padding: 1.25rem 0;
  box-shadow: none;
}

.events-block__item {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  border-radius: 1rem;
  border: 1px solid var(--slate-200);
  background-color: var(--white);
  padding: 1.5rem;
  box-shadow: 0 1px 2px 0 rgba(15, 23, 42, 0.08);
}

.events-block__item--placeholder {
  color: var(--slate-500);
  font-style: italic;
}

.events-block__title {
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--slate-900);
}

.events-block__date {
  display: inline-flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  border-radius: 0.75rem;
  border: 1px solid var(--slate-200);
  background-color: var(--slate-50);
  padding: 0.5rem 0.75rem;
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--slate-700);
}

.events-block__date-month {
  text-transform: uppercase;
  letter-spacing: 0.2em;
  font-size: 10px;
  color: var(--slate-500);
}

.events-block__date-day {
  font-size: 1.125rem;
  font-weight: 600;
  color: var(--slate-900);
}

.events-block__price {
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--slate-700);
}

.events-block__location,
.events-block__time {
  font-size: 0.875rem;
  color: var(--slate-500);
}

.events-block__meta {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
  font-size: 0.875rem;
  color: var(--slate-500);
}

.events-block__meta-line {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 0.5rem;
}

.events-block__badge {
  display: inline-flex;
  align-items: center;
  border-radius: 9999px;
  background-color: var(--primary-50);
  padding: 0.25rem 0.75rem;
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--primary-700);
}

.events-block__description {
  color: var(--slate-600);
}

.events-block__cta {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--primary-600);
}

.events-block__cta:hover {
  color: var(--primary-700);
}

.events-block__image {
  height: 10rem;
  width: 100%;
  border-radius: 0.75rem;
  object-fit: cover;
}

.events-block__media-fallback {
  display: flex;
  height: 10rem;
  align-items: center;
  justify-content: center;
  border-radius: 0.75rem;
  background-color: var(--slate-100);
  color: var(--slate-400);
}

@media (min-width: 768px) {
  h1 {
    font-size: 3rem;
  }

  h2 {
    font-size: 2.25rem;
  }

  h3 {
    font-size: 1.875rem;
  }

  h4 {
    font-size: 1.5rem;
  }

  .section {
    padding: 5rem 0;
  }

  .col-md-4 {
    width: 33.3333%;
  }

  .col-md-6 {
    width: 50%;
  }

  .col-md-8 {
    width: 66.6667%;
  }

  .display-5 {
    font-size: 3rem;
  }

  .display-6 {
    font-size: 2.25rem;
  }

  .hero-small {
    padding: 5rem 0;
  }

  .hero-medium {
    padding: 7rem 0;
  }

  .hero-large {
    padding: 9rem 0;
  }

  .hero-side {
    padding: 6rem 0;
  }

  .cta-banner__inner {
    padding: 4rem 0;
  }

  .cta-banner__headline {
    font-size: 2.25rem;
  }

  .map-block__body {
    grid-template-columns: 0.45fr 0.55fr;
  }

  .blog-post-list .blog-posts {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .events-block__items {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .events-block--layout-list .events-block__item {
    flex-direction: row;
    align-items: flex-start;
  }
}

@media (min-width: 1024px) {
  .col-lg-4 {
    width: 33.3333%;
  }

  .col-lg-5 {
    width: 41.6667%;
  }

  .col-lg-8 {
    width: 66.6667%;
  }

  .col-lg-9 {
    width: 75%;
  }

  .order-lg-first {
    order: -9999;
  }

  .order-lg-last {
    order: 9999;
  }
}

@media (min-width: 1280px) {
  .col-xl-3 {
    width: 25%;
  }

  .blog-post-list .blog-posts {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }

  .events-block__items {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
}
</style>
