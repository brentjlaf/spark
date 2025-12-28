<?php
// Shared admin styles without Tailwind runtime.
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
:root {
  --primary-50: #eef2ff;
  --primary-100: #e0e7ff;
  --primary-200: #c7d2fe;
  --primary-300: #a5b4fc;
  --primary-400: #818cf8;
  --primary-500: #6366f1;
  --primary-600: #4f46e5;
  --primary-700: #4338ca;
  --primary-800: #3730a3;
  --primary-900: #312e81;
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
  --green-50: #f0fdf4;
  --green-600: #16a34a;
  --amber-50: #fffbeb;
  --amber-300: #fcd34d;
  --amber-700: #b45309;
  --red-50: #fef2f2;
  --red-500: #ef4444;
  --red-600: #dc2626;
  --red-700: #b91c1c;
  --white: #ffffff;
}

* {
  box-sizing: border-box;
}

body {
  background-color: var(--slate-50);
  color: var(--slate-900);
  font-family: 'Poppins', 'Inter', system-ui, sans-serif;
}

a {
  color: var(--primary-600);
  transition: color 0.2s ease;
}

a:hover {
  color: var(--primary-700);
}

.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border-width: 0;
}

.admin-container {
  min-height: 100vh;
  display: flex;
  background-color: var(--slate-50);
  color: var(--slate-900);
}

.sidebar {
  position: fixed;
  inset: 0 auto 0 0;
  width: 18rem;
  background-color: var(--white);
  border-right: 1px solid var(--slate-200);
  box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.1);
  overflow-y: auto;
  transform: translateX(-100%);
  transition: transform 0.2s ease-out;
  z-index: 40;
}

.sidebar.mobile-open {
  transform: translateX(0);
}

.sidebar-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem 1.25rem;
  border-bottom: 1px solid var(--slate-200);
}

.sidebar-logo {
  font-size: 1.125rem;
  font-weight: 600;
  color: var(--slate-800);
}

.sidebar-nav {
  padding: 1rem 0.75rem;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.nav-section {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.nav-section-title {
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.15em;
  color: var(--slate-400);
  font-weight: 600;
  padding: 0 0.75rem;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.5rem 0.75rem;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--slate-700);
  cursor: pointer;
  transition: background-color 0.2s ease, color 0.2s ease;
}

.nav-item:hover {
  background-color: var(--primary-50);
  color: var(--primary-700);
}

.nav-item.active {
  background-color: var(--primary-600);
  color: var(--white);
  box-shadow: 0 1px 2px 0 rgba(15, 23, 42, 0.08);
}

.nav-item .nav-icon {
  width: 1.25rem;
  height: 1.25rem;
  color: var(--slate-400);
  display: flex;
  align-items: center;
  justify-content: center;
}

.nav-item:hover .nav-icon {
  color: var(--primary-700);
}

.nav-item.active .nav-icon {
  color: var(--white);
}

.nav-text a {
  display: block;
  width: 100%;
  color: inherit;
  text-decoration: none;
}

.sidebar-overlay {
  position: fixed;
  inset: 0;
  background-color: rgba(15, 23, 42, 0.4);
  z-index: 30;
  display: none;
}

.sidebar-overlay.active {
  display: block;
}

.main-content {
  flex: 1;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.top-bar {
  position: sticky;
  top: 0;
  z-index: 20;
  height: 4rem;
  background-color: var(--white);
  border-bottom: 1px solid var(--slate-200);
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 1rem;
  box-shadow: 0 1px 2px 0 rgba(15, 23, 42, 0.08);
}

.menu-toggle {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 2.5rem;
  height: 2.5rem;
  border-radius: 0.5rem;
  border: 1px solid var(--slate-200);
  color: var(--slate-600);
  background: none;
}

.menu-toggle:hover {
  background-color: var(--slate-50);
}

.page-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: var(--slate-800);
}

.top-bar-actions {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.search-box {
  display: none;
  align-items: center;
  gap: 0.5rem;
  background-color: var(--slate-100);
  border: 1px solid var(--slate-200);
  border-radius: 0.5rem;
  padding: 0.5rem 0.75rem;
}

.search-input {
  background-color: transparent;
  outline: none;
  font-size: 0.875rem;
  flex: 1;
  border: none;
}

.search-icon {
  color: var(--slate-400);
}

.user-menu {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding-left: 1rem;
  border-left: 1px solid var(--slate-200);
}

.user-avatar {
  width: 2.5rem;
  height: 2.5rem;
  border-radius: 9999px;
  background-color: var(--primary-600);
  color: var(--white);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
}

.user-info {
  display: none;
}

.user-name {
  font-size: 0.875rem;
  font-weight: 600;
}

.content-area {
  padding: 1rem;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.content-section {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.a11y-dashboard {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.a11y-hero {
  background-color: var(--white);
  border: 1px solid var(--slate-200);
  border-radius: 1rem;
  padding: 1.5rem;
  box-shadow: 0 1px 2px 0 rgba(15, 23, 42, 0.08);
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.a11y-hero-content {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.hero-eyebrow,
.dashboard-hero-eyebrow,
.forms-hero-eyebrow,
.analytics-hero-eyebrow {
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.15em;
  color: var(--primary-600);
}

.a11y-hero-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--slate-900);
}

.a11y-hero-subtitle {
  color: var(--slate-600);
  max-width: 48rem;
}

.a11y-hero-actions {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 0.75rem;
}

.a11y-hero-meta {
  font-size: 0.875rem;
  color: var(--slate-500);
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.a11y-overview-grid {
  display: grid;
  gap: 1rem;
}

.a11y-overview-card {
  background-color: var(--primary-50);
  border: 1px solid var(--primary-100);
  border-radius: 0.75rem;
  padding: 1rem;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.a11y-overview-label {
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  color: var(--slate-500);
}

.a11y-overview-value {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--slate-900);
}

.a11y-overview-delta {
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--green-600);
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.dashboard-panel {
  background-color: var(--white);
  border: 1px solid var(--slate-200);
  border-radius: 1rem;
  box-shadow: 0 1px 2px 0 rgba(15, 23, 42, 0.08);
  padding: 1.25rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.dashboard-panel-header {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.dashboard-panel-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: var(--slate-900);
}

.dashboard-panel-description {
  font-size: 0.875rem;
  color: var(--slate-600);
}

.dashboard-quick-actions,
.dashboard-module-card-grid {
  display: grid;
  gap: 1rem;
}

.dashboard-quick-card,
.dashboard-module-card {
  border: 1px solid var(--slate-200);
  border-radius: 0.75rem;
  background-color: var(--slate-50);
  padding: 1rem;
  display: flex;
  align-items: flex-start;
  gap: 0.75rem;
  box-shadow: 0 1px 2px 0 rgba(15, 23, 42, 0.08);
}

.dashboard-quick-card.placeholder,
.dashboard-module-card.placeholder {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.a11y-detail-card {
  background-color: var(--white);
  border: 1px solid var(--slate-200);
  border-radius: 1rem;
  box-shadow: 0 1px 2px 0 rgba(15, 23, 42, 0.08);
  padding: 1.25rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.a11y-detail-header {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.a11y-detail-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
  align-items: center;
}

.a11y-detail-content {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.a11y-detail-meta {
  display: grid;
  grid-template-columns: 1fr;
  gap: 0.75rem;
}

.a11y-detail-meta__label {
  font-size: 0.875rem;
  color: var(--slate-500);
}

.a11y-detail-meta__value {
  font-weight: 600;
  color: var(--slate-900);
}

.a11y-detail-grid {
  display: grid;
  gap: 1rem;
}

.a11y-empty-state,
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  gap: 0.75rem;
  padding: 1.5rem;
  border: 1px dashed var(--slate-300);
  border-radius: 0.75rem;
  background-color: var(--slate-50);
  color: var(--slate-600);
}

.empty-state {
  border-radius: 1rem;
  background-color: var(--white);
  box-shadow: 0 1px 2px 0 rgba(15, 23, 42, 0.08);
}

.empty-state__icon {
  width: 3rem;
  height: 3rem;
  border-radius: 9999px;
  background-color: var(--primary-50);
  color: var(--primary-600);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem;
}

.empty-state__content {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.empty-state__title {
  font-size: 1.125rem;
  font-weight: 600;
  color: var(--slate-900);
}

.empty-state__description {
  font-size: 0.875rem;
  color: var(--slate-600);
  max-width: 28rem;
}

.empty-state__cta {
  margin-top: 0.5rem;
}

.a11y-pages-grid {
  display: grid;
  gap: 0.75rem;
}

.a11y-page-detail {
  position: fixed;
  inset: 0;
  z-index: 40;
  display: flex;
  align-items: flex-start;
  justify-content: flex-end;
  background-color: rgba(15, 23, 42, 0.5);
}

.a11y-page-detail .a11y-detail-content {
  max-width: 48rem;
  width: 100%;
  background-color: var(--white);
  height: 100vh;
  overflow-y: auto;
  padding: 1.5rem;
  box-shadow: -10px 0 25px rgba(15, 23, 42, 0.25);
}

.a11y-detail-close {
  position: absolute;
  top: 1rem;
  right: 1rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 2.5rem;
  height: 2.5rem;
  border-radius: 9999px;
  border: 1px solid var(--slate-200);
  color: var(--slate-500);
  background: none;
}

.a11y-detail-close:hover {
  background-color: var(--slate-50);
}

.a11y-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  font-weight: 600;
  transition: background-color 0.15s ease, color 0.15s ease;
  border: none;
}

.a11y-btn--primary {
  background-color: var(--primary-600);
  color: var(--white);
  box-shadow: 0 1px 2px 0 rgba(15, 23, 42, 0.08);
}

.a11y-btn--primary:hover {
  background-color: var(--primary-700);
}

.a11y-btn--secondary {
  background-color: var(--slate-900);
  color: var(--white);
}

.a11y-btn--secondary:hover {
  background-color: var(--slate-800);
}

.a11y-btn--ghost {
  background-color: var(--white);
  color: var(--slate-700);
  border: 1px solid var(--slate-200);
}

.a11y-btn--ghost:hover {
  background-color: var(--slate-50);
}

.a11y-btn--danger {
  background-color: var(--red-500);
  color: var(--white);
}

.a11y-btn--danger:hover {
  background-color: var(--red-600);
}

.a11y-btn--icon {
  padding: 0.5rem;
  border-radius: 9999px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.form-label {
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--slate-700);
}

.form-input,
.form-control {
  width: 100%;
  border-radius: 0.5rem;
  border: 1px solid var(--slate-300);
  padding: 0.5rem 0.75rem;
  font-size: 0.875rem;
  outline: none;
}

.form-input:focus,
.form-control:focus {
  border-color: var(--primary-500);
  box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.3);
}

.form-textarea,
textarea.form-control {
  width: 100%;
  border-radius: 0.5rem;
  border: 1px solid var(--slate-300);
  padding: 0.5rem 0.75rem;
  font-size: 0.875rem;
  min-height: 140px;
  outline: none;
}

.form-textarea:focus,
textarea.form-control:focus {
  border-color: var(--primary-500);
  box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.3);
}

.form-select,
select.form-control {
  width: 100%;
  border-radius: 0.5rem;
  border: 1px solid var(--slate-300);
  padding: 0.5rem 0.75rem;
  font-size: 0.875rem;
  background-color: var(--white);
  outline: none;
}

.form-select:focus,
select.form-control:focus {
  border-color: var(--primary-500);
  box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.3);
}

.form-check {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
  color: var(--slate-700);
}

.form-alert,
.alert {
  border-radius: 0.5rem;
  border: 1px solid var(--amber-300);
  background-color: var(--amber-50);
  padding: 0.75rem 1rem;
  color: var(--amber-700);
  font-size: 0.875rem;
}

.badge,
.status-badge,
.category-tag,
.issue-tag,
.a11y-issue-tag,
.import-status {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
  background-color: var(--primary-50);
  color: var(--primary-700);
}

.status-good,
.status-published,
.user-status--active,
.import-status--success {
  background-color: var(--green-50);
  color: var(--green-600);
}

.status-warning,
.a11y-issue-tag.serious,
.import-status--warning {
  background-color: var(--amber-50);
  color: var(--amber-700);
}

.status-critical,
.user-status--inactive,
.import-status--error {
  background-color: var(--red-50);
  color: var(--red-700);
}

.table,
.a11y-table-view table {
  width: 100%;
  border-collapse: collapse;
}

.table th,
.a11y-table-header {
  text-align: left;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: var(--slate-500);
  border-bottom: 1px solid var(--slate-200);
  padding-bottom: 0.5rem;
}

.table td,
.a11y-table-view td {
  border-bottom: 1px solid var(--slate-100);
  padding: 0.75rem 0;
  font-size: 0.875rem;
  color: var(--slate-700);
}

.a11y-table-view {
  overflow: hidden;
  border-radius: 0.75rem;
  border: 1px solid var(--slate-200);
  background-color: var(--white);
  box-shadow: 0 1px 2px 0 rgba(15, 23, 42, 0.08);
}

.a11y-table-header-row {
  background-color: var(--slate-50);
}

.table-responsive {
  overflow-x: auto;
}

.content {
  max-width: 64rem;
  margin: 0 auto;
  padding: 1.5rem;
}

.footer {
  text-align: center;
  font-size: 0.875rem;
  color: var(--slate-500);
  padding: 1.5rem 0;
}

.header {
  background-color: var(--white);
  border-bottom: 1px solid var(--slate-200);
  padding: 1rem 1.5rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  font-weight: 600;
  background-color: var(--primary-600);
  color: var(--white);
  border: none;
  box-shadow: 0 1px 2px 0 rgba(15, 23, 42, 0.08);
}

.btn:hover {
  background-color: var(--primary-700);
}

.btn.btn-secondary,
.btn-secondary {
  background-color: var(--white);
  color: var(--slate-700);
  border: 1px solid var(--slate-200);
}

.btn.btn-secondary:hover,
.btn-secondary:hover {
  background-color: var(--slate-50);
}

.btn-icon {
  width: 1rem;
  height: 1rem;
}

.input-group {
  display: flex;
  border-radius: 0.5rem;
  border: 1px solid var(--slate-300);
  overflow: hidden;
}

.input-group .form-control {
  border: 0;
  border-radius: 0;
  flex: 1;
}

.input-group .btn {
  border-radius: 0;
  padding: 0.5rem 0.75rem;
}

.login-container {
  max-width: 28rem;
  margin: 6rem auto 0;
  background-color: var(--white);
  border: 1px solid var(--slate-200);
  border-radius: 1rem;
  box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.1);
  padding: 2rem;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.login-container h2 {
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--slate-900);
  text-align: center;
}

.login-container label {
  display: block;
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--slate-700);
}

.login-container input {
  width: 100%;
  border-radius: 0.5rem;
  border: 1px solid var(--slate-300);
  padding: 0.5rem 0.75rem;
  font-size: 0.875rem;
  outline: none;
}

.login-container input:focus {
  border-color: var(--primary-500);
  box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.3);
}

.login-container .error {
  border-radius: 0.5rem;
  background-color: var(--red-50);
  border: 1px solid var(--red-500);
  color: var(--red-700);
  padding: 0.75rem 1rem;
  font-size: 0.875rem;
}

.login-container form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.forms-main-grid {
  display: grid;
  gap: 1rem;
}

.forms-library,
.forms-submissions-container {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.forms-submission-card {
  border-radius: 0.75rem;
  border: 1px solid var(--slate-200);
  background-color: var(--white);
  box-shadow: 0 1px 2px 0 rgba(15, 23, 42, 0.08);
  padding: 1rem;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.forms-submissions-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.forms-submissions-empty {
  text-align: center;
  color: var(--slate-500);
  border: 1px dashed var(--slate-300);
  border-radius: 0.75rem;
  padding: 2rem 0;
}

.forms-drawer {
  position: fixed;
  inset: 0;
  background-color: rgba(15, 23, 42, 0.5);
  z-index: 40;
  display: flex;
  align-items: flex-start;
  justify-content: flex-end;
}

.forms-drawer .a11y-detail-content {
  width: 100%;
  max-width: 40rem;
  background-color: var(--white);
  height: 100vh;
  overflow-y: auto;
  padding: 1.5rem;
}

.media-grid,
.gallery-grid {
  display: grid;
  gap: 1rem;
}

.media-card {
  border-radius: 0.75rem;
  border: 1px solid var(--slate-200);
  background-color: var(--white);
  box-shadow: 0 1px 2px 0 rgba(15, 23, 42, 0.08);
  padding: 1rem;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.search-box input::placeholder {
  color: var(--slate-400);
}

@keyframes pulse {
  50% {
    opacity: 0.5;
  }
}

@media (min-width: 640px) {
  .user-info {
    display: block;
  }

  .a11y-pages-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (min-width: 768px) {
  .sidebar {
    position: sticky;
    top: 0;
    height: 100vh;
    transform: translateX(0);
    box-shadow: none;
  }

  .top-bar {
    padding: 0 1.5rem;
  }

  .content-area {
    padding: 1.5rem;
  }

  .a11y-hero-content {
    flex-direction: row;
    align-items: flex-start;
    justify-content: space-between;
  }

  .dashboard-panel-header {
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
  }

  .a11y-detail-header {
    flex-direction: row;
    align-items: flex-start;
    justify-content: space-between;
  }

  .a11y-detail-meta {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .a11y-detail-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (min-width: 1024px) {
  .search-box {
    display: flex;
  }

  .content-area {
    padding: 1.5rem;
  }

  .a11y-overview-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .dashboard-quick-actions,
  .dashboard-module-card-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .a11y-detail-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .forms-main-grid {
    grid-template-columns: 2fr 1.3fr;
  }

  .media-grid,
  .gallery-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (min-width: 1280px) {
  .a11y-overview-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }

  .dashboard-quick-actions,
  .dashboard-module-card-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }

  .a11y-detail-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }

  .media-grid,
  .gallery-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
}
</style>
