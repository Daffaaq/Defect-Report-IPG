<?php
$pageTitle = "Input Form Templates";
include '../layout/head.php';
?>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">

<style>
    /* Styling spesifik untuk halaman ini */
    .bg-gradient-2 {
        background: linear-gradient(135deg, #0891b2 0%, #059669 100%);
        color: white;
    }

    .chart-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 1.5rem;
        overflow: visible;
    }

    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .chart-header h4 {
        color: var(--text-primary);
        margin: 0;
        font-weight: 600;
    }

    .chart-header h4 i {
        color: var(--accent-3);
    }

    /* Form styling */
    .form-section {
        background: var(--bg-secondary);
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid var(--border-color);
    }

    .form-section-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid var(--accent-3);
        display: flex;
        align-items: center;
    }

    .form-section-title i {
        color: var(--accent-3);
        margin-right: 0.75rem;
        font-size: 1.2rem;
    }

    .form-group {
        margin-bottom: 1.25rem;
    }

    .form-label {
        color: var(--text-secondary);
        font-weight: 500;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    .form-label i {
        color: var(--accent-3);
        margin-right: 0.5rem;
        width: 16px;
    }

    .form-control,
    .form-select {
        background: var(--card-bg);
        border: 2px solid var(--border-color);
        color: var(--text-primary);
        border-radius: 12px;
        padding: 0.6rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .form-control:focus,
    .form-select:focus {
        background: var(--card-bg);
        border-color: var(--accent-3);
        color: var(--text-primary);
        box-shadow: 0 0 0 0.25rem rgba(8, 145, 178, 0.15);
    }

    .form-control[readonly] {
        background: var(--bg-secondary);
        cursor: default;
    }

    /* Input group styling */
    .input-group {
        border-radius: 12px;
        overflow: hidden;
    }

    .input-group-text {
        background: var(--accent-3);
        color: white;
        border: 2px solid var(--accent-3);
        padding: 0.6rem 1rem;
    }

    .input-group .form-control {
        border-left: none;
    }

    /* Double input styling */
    .double-input {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .double-input .form-group {
        margin-bottom: 0;
    }

    /* Dynamic input styling */
    .dynamic-input-wrapper {
        background: var(--bg-secondary);
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1rem;
        border: 1px solid var(--border-color);
    }

    .dynamic-item {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 1rem;
        margin-bottom: 1rem;
        align-items: center;
    }

    .dynamic-item:last-child {
        margin-bottom: 0;
    }

    .dynamic-item .form-group {
        margin-bottom: 0;
    }

    .btn-add-dynamic {
        background: var(--accent-3);
        color: white;
        border: none;
        border-radius: 50px;
        padding: 0.5rem 1.5rem;
        font-size: 0.9rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 1rem;
    }

    .btn-add-dynamic:hover {
        background: linear-gradient(135deg, #0891b2 0%, #059669 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(8, 145, 178, 0.3);
    }

    .btn-remove-dynamic {
        background: #dc2626;
        color: white;
        border: none;
        border-radius: 50px;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-remove-dynamic:hover {
        background: #b91c1c;
        transform: scale(1.05);
    }

    /* Select2 styling */
    .select2-container--bootstrap-5 .select2-selection {
        background: var(--card-bg);
        border: 2px solid var(--border-color);
        border-radius: 12px;
        min-height: 42px;
    }

    .select2-container--bootstrap-5.select2-container--focus .select2-selection {
        border-color: var(--accent-3);
        box-shadow: 0 0 0 0.25rem rgba(8, 145, 178, 0.15);
    }

    .select2-container--bootstrap-5 .select2-dropdown {
        background: var(--card-bg);
        border-color: var(--border-color);
    }

    .select2-container--bootstrap-5 .select2-results__option {
        color: var(--text-primary);
    }

    .select2-container--bootstrap-5 .select2-results__option--highlighted {
        background: var(--accent-3);
    }

    /* Flatpickr styling */
    .flatpickr-calendar {
        background: var(--card-bg);
        border-color: var(--border-color);
    }

    .flatpickr-months .flatpickr-month,
    .flatpickr-weekdays,
    .flatpickr-weekday {
        background: var(--accent-3);
        color: white;
    }

    .flatpickr-day {
        color: var(--text-primary);
    }

    .flatpickr-day:hover {
        background: var(--hover-bg);
    }

    .flatpickr-day.selected {
        background: var(--accent-3);
        border-color: var(--accent-3);
    }

    /* Range input styling */
    .range-wrapper {
        background: var(--bg-secondary);
        border-radius: 12px;
        padding: 1rem;
    }

    .range-values {
        display: flex;
        justify-content: space-between;
        margin-top: 0.5rem;
        color: var(--text-secondary);
        font-size: 0.9rem;
    }

    /* File input styling */
    .file-upload-wrapper {
        border: 2px dashed var(--border-color);
        border-radius: 12px;
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .file-upload-wrapper:hover {
        border-color: var(--accent-3);
        background: var(--hover-bg);
    }

    .file-upload-wrapper i {
        font-size: 2.5rem;
        color: var(--accent-3);
        margin-bottom: 1rem;
    }

    .file-upload-wrapper .file-info {
        color: var(--text-secondary);
        font-size: 0.9rem;
        margin-top: 0.5rem;
    }

    /* Toggle switch styling */
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: var(--border-color);
        transition: .4s;
        border-radius: 34px;
    }

    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked+.toggle-slider {
        background: linear-gradient(135deg, #0891b2 0%, #059669 100%);
    }

    input:checked+.toggle-slider:before {
        transform: translateX(26px);
    }

    /* Code block styling */
    .code-block {
        background: var(--bg-secondary);
        border-radius: 12px;
        padding: 1rem;
        margin-top: 0.5rem;
        border: 1px solid var(--border-color);
        font-family: 'Courier New', monospace;
        font-size: 0.85rem;
        color: var(--text-primary);
        position: relative;
    }

    .copy-btn {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        background: var(--accent-3);
        color: white;
        border: none;
        border-radius: 6px;
        padding: 0.25rem 0.75rem;
        font-size: 0.8rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .copy-btn:hover {
        background: linear-gradient(135deg, #0891b2 0%, #059669 100%);
    }

    /* Tab styling */
    .nav-tabs {
        border-bottom: 2px solid var(--border-color);
        margin-bottom: 1.5rem;
    }

    .nav-tabs .nav-link {
        color: var(--text-secondary);
        border: none;
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .nav-tabs .nav-link:hover {
        color: var(--accent-3);
        border: none;
    }

    .nav-tabs .nav-link.active {
        color: var(--accent-3);
        background: transparent;
        border-bottom: 3px solid var(--accent-3);
    }

    /* Grid system untuk form */
    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .form-row:last-child {
        margin-bottom: 0;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .chart-card {
            padding: 1rem;
        }

        .form-section {
            padding: 1rem;
        }

        .double-input {
            grid-template-columns: 1fr;
            gap: 0.5rem;
        }

        .dynamic-item {
            grid-template-columns: 1fr;
        }

        .btn-remove-dynamic {
            width: 100%;
        }

        .form-row {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .nav-tabs .nav-link {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }

        /* Validation styling */
        .valid-feedback {
            color: #10b981;
            font-size: 0.85rem;
            margin-top: 0.25rem;
        }

        .invalid-feedback {
            color: #ef4444;
            font-size: 0.85rem;
            margin-top: 0.25rem;
        }

        .form-control.is-valid,
        .form-select.is-valid {
            border-color: #10b981;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2310b981' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }

        .form-control.is-invalid,
        .form-select.is-invalid {
            border-color: #ef4444;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23ef4444'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23ef4444' stroke='none'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }

        .was-validated .form-control:invalid:focus,
        .form-control.is-invalid:focus {
            border-color: #ef4444;
            box-shadow: 0 0 0 0.25rem rgba(239, 68, 68, 0.25);
        }

        .was-validated .form-control:valid:focus,
        .form-control.is-valid:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 0.25rem rgba(16, 185, 129, 0.25);
        }
    }
</style>

<?php include '../layout/sidebar.php'; ?>
<?php include '../layout/header.php'; ?>

<!-- Content -->
<div class="chart-card">
    <div class="chart-header">
        <h4><i class="fas fa-input-text me-2"></i>Input Form Templates</h4>
        <div>
            <button class="btn btn-primary" id="showCodeBtn">
                <i class="fas fa-code me-2"></i>Show Code
            </button>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs" id="formTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="single-tab" data-bs-toggle="tab" data-bs-target="#single" type="button" role="tab">
                <i class="fas fa-input-text me-2"></i>Single Inputs
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="double-tab" data-bs-toggle="tab" data-bs-target="#double" type="button" role="tab">
                <i class="fas fa-columns me-2"></i>Double Inputs
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="dynamic-tab" data-bs-toggle="tab" data-bs-target="#dynamic" type="button" role="tab">
                <i class="fas fa-dynamic me-2"></i>Dynamic Inputs
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="advanced-tab" data-bs-toggle="tab" data-bs-target="#advanced" type="button" role="tab">
                <i class="fas fa-cog me-2"></i>Advanced Inputs
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="inputgroup-tab" data-bs-toggle="tab" data-bs-target="#inputgroup" type="button" role="tab">
                <i class="fas fa-object-group me-2"></i>Input Groups
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="validation-tab" data-bs-toggle="tab" data-bs-target="#validation" type="button" role="tab">
                <i class="fas fa-check-circle me-2"></i>Validation
            </button>
        </li>
    </ul>

    <!-- Tabs Content -->
    <div class="tab-content" id="formTabsContent">
        <!-- SINGLE INPUTS TAB -->
        <div class="tab-pane fade show active" id="single" role="tabpanel">
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-input-text"></i>
                    Single Input Fields
                    <span class="ms-auto badge bg-gradient-2">Basic Inputs</span>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-user"></i>
                            Text Input
                        </label>
                        <input type="text" class="form-control" placeholder="Enter text...">
                        <div class="code-block mt-2">
                            &lt;input type="text" class="form-control"&gt;
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-envelope"></i>
                            Email Input
                        </label>
                        <input type="email" class="form-control" placeholder="user@example.com">
                        <div class="code-block mt-2">
                            &lt;input type="email" class="form-control"&gt;
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-lock"></i>
                            Password Input
                        </label>
                        <input type="password" class="form-control" placeholder="********">
                        <div class="code-block mt-2">
                            &lt;input type="password" class="form-control"&gt;
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-phone"></i>
                            Telephone Input
                        </label>
                        <input type="tel" class="form-control" placeholder="+62 812-3456-7890">
                        <div class="code-block mt-2">
                            &lt;input type="tel" class="form-control"&gt;
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-globe"></i>
                            URL Input
                        </label>
                        <input type="url" class="form-control" placeholder="https://example.com">
                        <div class="code-block mt-2">
                            &lt;input type="url" class="form-control"&gt;
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-hashtag"></i>
                            Number Input
                        </label>
                        <input type="number" class="form-control" min="0" max="100" step="1">
                        <div class="code-block mt-2">
                            &lt;input type="number" min="0" max="100"&gt;
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-search"></i>
                            Search Input
                        </label>
                        <input type="search" class="form-control" placeholder="Search...">
                        <div class="code-block mt-2">
                            &lt;input type="search" class="form-control"&gt;
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-calendar"></i>
                            Date Input
                        </label>
                        <input type="date" class="form-control">
                        <div class="code-block mt-2">
                            &lt;input type="date" class="form-control"&gt;
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-clock"></i>
                            Time Input
                        </label>
                        <input type="time" class="form-control">
                        <div class="code-block mt-2">
                            &lt;input type="time" class="form-control"&gt;
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-align-left"></i>
                            Textarea
                        </label>
                        <textarea class="form-control" rows="3" placeholder="Enter text..."></textarea>
                        <div class="code-block mt-2">
                            &lt;textarea class="form-control" rows="3"&gt;&lt;/textarea&gt;
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-list"></i>
                            Select Dropdown
                        </label>
                        <select class="form-control">
                            <option value="">-- Select Option --</option>
                            <option value="1">Option 1</option>
                            <option value="2">Option 2</option>
                            <option value="3">Option 3</option>
                        </select>
                        <div class="code-block mt-2">
                            &lt;select class="form-control"&gt;<br>
                            &nbsp;&nbsp;&lt;option&gt;-- Select --&lt;/option&gt;<br>
                            &lt;/select&gt;
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-check-circle"></i>
                            Color Picker
                        </label>
                        <input type="color" class="form-control form-control-color" value="#0891b2">
                        <div class="code-block mt-2">
                            &lt;input type="color" class="form-control"&gt;
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-ban"></i>
                            Disabled Input
                        </label>
                        <input type="text" class="form-control" value="Disabled field" disabled>
                        <div class="code-block mt-2">
                            &lt;input type="text" disabled&gt;
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-bookmark"></i>
                            Readonly Input
                        </label>
                        <input type="text" class="form-control" value="Readonly field" readonly>
                        <div class="code-block mt-2">
                            &lt;input type="text" readonly&gt;
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-check-square"></i>
                            Checkbox
                        </label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="checkbox1" checked>
                            <label class="form-check-label" for="checkbox1">Checkbox 1</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="checkbox2">
                            <label class="form-check-label" for="checkbox2">Checkbox 2</label>
                        </div>
                        <div class="code-block mt-2">
                            &lt;input type="checkbox" class="form-check-input"&gt;
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- DOUBLE INPUTS TAB -->
        <div class="tab-pane fade" id="double" role="tabpanel">
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-columns"></i>
                    Double Input Fields
                    <span class="ms-auto badge bg-gradient-2">Paired Inputs</span>
                </div>

                <div class="double-input mb-3">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-user"></i>
                            First Name
                        </label>
                        <input type="text" class="form-control" placeholder="Enter first name">
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-user"></i>
                            Last Name
                        </label>
                        <input type="text" class="form-control" placeholder="Enter last name">
                    </div>
                </div>
                <div class="code-block mb-4">
                    &lt;div class="double-input"&gt;<br>
                    &nbsp;&nbsp;&lt;div class="form-group"&gt;...&lt;/div&gt;<br>
                    &nbsp;&nbsp;&lt;div class="form-group"&gt;...&lt;/div&gt;<br>
                    &lt;/div&gt;
                </div>

                <div class="double-input mb-3">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-calendar"></i>
                            Start Date
                        </label>
                        <input type="date" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-calendar"></i>
                            End Date
                        </label>
                        <input type="date" class="form-control">
                    </div>
                </div>

                <div class="double-input mb-3">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-clock"></i>
                            Start Time
                        </label>
                        <input type="time" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-clock"></i>
                            End Time
                        </label>
                        <input type="time" class="form-control">
                    </div>
                </div>

                <div class="double-input mb-3">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-map-marker-alt"></i>
                            Latitude
                        </label>
                        <input type="number" class="form-control" step="0.000001" placeholder="-6.2088">
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-map-marker-alt"></i>
                            Longitude
                        </label>
                        <input type="number" class="form-control" step="0.000001" placeholder="106.8456">
                    </div>
                </div>

                <div class="double-input mb-3">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-arrows-alt-h"></i>
                            Width (cm)
                        </label>
                        <input type="number" class="form-control" min="0" step="0.1">
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-arrows-alt-v"></i>
                            Height (cm)
                        </label>
                        <input type="number" class="form-control" min="0" step="0.1">
                    </div>
                </div>

                <div class="double-input mb-3">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-arrows-alt"></i>
                            Min Value
                        </label>
                        <input type="number" class="form-control" value="0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-arrows-alt"></i>
                            Max Value
                        </label>
                        <input type="number" class="form-control" value="100">
                    </div>
                </div>

                <div class="double-input">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-building"></i>
                            Department (Select2)
                        </label>
                        <select class="form-control demo-select2-department">
                            <option value="">-- Select --</option>
                            <option value="IT">IT</option>
                            <option value="HR">HR</option>
                            <option value="Finance">Finance</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-briefcase"></i>
                            Position (Biasa)
                        </label>
                        <select class="form-control">
                            <option value="">-- Select --</option>
                            <option value="Manager">Manager</option>
                            <option value="Staff">Staff</option>
                            <option value="Intern">Intern</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- DYNAMIC INPUTS TAB -->
        <div class="tab-pane fade" id="dynamic" role="tabpanel">
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-dynamic"></i>
                    Dynamic Input Fields
                    <span class="ms-auto badge bg-gradient-2">Add/Remove</span>
                </div>

                <div class="dynamic-input-wrapper">
                    <label class="form-label">
                        <i class="fas fa-phone"></i>
                        Phone Numbers (Dynamic)
                    </label>
                    <div id="demoPhoneContainer">
                        <div class="dynamic-item">
                            <div class="form-group">
                                <input type="tel" class="form-control" placeholder="Enter phone number">
                            </div>
                            <button type="button" class="btn-remove-dynamic" onclick="demoRemoveItem(this)">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <button type="button" class="btn-add-dynamic" onclick="demoAddPhone()">
                        <i class="fas fa-plus"></i> Add Phone Number
                    </button>
                    <div class="code-block mt-3">
                        // JavaScript function to add dynamic input<br>
                        function addPhoneNumber() {<br>
                        &nbsp;&nbsp;const container = $('#phoneContainer');<br>
                        &nbsp;&nbsp;const newItem = `&lt;div class="dynamic-item"&gt;...&lt;/div&gt;`;<br>
                        &nbsp;&nbsp;container.append(newItem);<br>
                        }
                    </div>
                </div>

                <div class="dynamic-input-wrapper">
                    <label class="form-label">
                        <i class="fas fa-tasks"></i>
                        Skills with Select (Dynamic)
                    </label>
                    <div id="demoSkillContainer">
                        <div class="dynamic-item">
                            <div class="form-group">
                                <select class="form-control">
                                    <option value="">-- Select Skill --</option>
                                    <option value="PHP">PHP</option>
                                    <option value="JavaScript">JavaScript</option>
                                    <option value="Python">Python</option>
                                </select>
                            </div>
                            <button type="button" class="btn-remove-dynamic" onclick="demoRemoveItem(this)">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <button type="button" class="btn-add-dynamic" onclick="demoAddSkill()">
                        <i class="fas fa-plus"></i> Add Skill
                    </button>
                </div>

                <div class="dynamic-input-wrapper">
                    <label class="form-label">
                        <i class="fas fa-calendar"></i>
                        Experience (Double Dynamic)
                    </label>
                    <div id="demoExpContainer">
                        <div class="dynamic-item" style="grid-template-columns: 1fr 1fr auto;">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Company">
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Position">
                            </div>
                            <button type="button" class="btn-remove-dynamic" onclick="demoRemoveItem(this)">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <button type="button" class="btn-add-dynamic" onclick="demoAddExperience()">
                        <i class="fas fa-plus"></i> Add Experience
                    </button>
                </div>
            </div>
        </div>

        <!-- ADVANCED INPUTS TAB -->
        <div class="tab-pane fade" id="advanced" role="tabpanel">
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-cog"></i>
                    Advanced Input Types
                    <span class="ms-auto badge bg-gradient-2">With Libraries</span>
                </div>

                <!-- Row 1: Date, Time, Date Range -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-calendar-alt"></i>
                            Date Picker (Flatpickr)
                        </label>
                        <input type="text" class="form-control demo-datepicker" placeholder="Select date">
                        <div class="code-block mt-2">
                            // Initialize Flatpickr<br>
                            $('.datepicker').flatpickr({<br>
                            &nbsp;&nbsp;dateFormat: "Y-m-d"<br>
                            });
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-clock"></i>
                            Time Picker
                        </label>
                        <input type="text" class="form-control demo-timepicker" placeholder="Select time">
                        <div class="code-block mt-2">
                            // Initialize Time Picker<br>
                            $('.timepicker').flatpickr({<br>
                            &nbsp;&nbsp;enableTime: true,<br>
                            &nbsp;&nbsp;noCalendar: true<br>
                            });
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-calendar-week"></i>
                            Date Range
                        </label>
                        <input type="text" class="form-control demo-daterangepicker" placeholder="Select date range">
                        <div class="code-block mt-2">
                            // Initialize Date Range<br>
                            $('.daterangepicker').flatpickr({<br>
                            &nbsp;&nbsp;mode: "range"<br>
                            });
                        </div>
                    </div>
                </div>

                <!-- Row 2: DateTime Picker -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-calendar-plus"></i>
                            DateTime Picker
                        </label>
                        <input type="text" class="form-control demo-datetimepicker" placeholder="Select date and time">
                        <div class="code-block mt-2">
                            // Initialize DateTime Picker<br>
                            $('.datetimepicker').flatpickr({<br>
                            &nbsp;&nbsp;enableTime: true,<br>
                            &nbsp;&nbsp;dateFormat: "Y-m-d H:i",<br>
                            &nbsp;&nbsp;time_24hr: true<br>
                            });
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-search"></i>
                            Select2 (Single)
                        </label>
                        <select class="form-control demo-select2-single">
                            <option value="">-- Select User --</option>
                            <option value="1">John Doe</option>
                            <option value="2">Jane Smith</option>
                            <option value="3">Budi Santoso</option>
                        </select>
                        <div class="code-block mt-2">
                            // Initialize Select2<br>
                            $('.select2-single').select2({<br>
                            &nbsp;&nbsp;theme: 'bootstrap-5'<br>
                            });
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-tags"></i>
                            Select2 (Multiple)
                        </label>
                        <select class="form-control demo-select2-multiple" multiple>
                            <option value="PHP">PHP</option>
                            <option value="JavaScript">JavaScript</option>
                            <option value="Python">Python</option>
                            <option value="Java">Java</option>
                        </select>
                        <div class="code-block mt-2">
                            // Initialize Select2 Multiple<br>
                            $('.select2-multiple').select2({<br>
                            &nbsp;&nbsp;theme: 'bootstrap-5'<br>
                            });
                        </div>
                    </div>
                </div>

                <!-- Row 3: Range Slider, File Upload, Toggle Switch -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-sliders-h"></i>
                            Range Slider
                        </label>
                        <div class="range-wrapper">
                            <input type="range" class="form-range" min="0" max="100" value="50" id="rangeSlider" oninput="updateRangeValue(this.value)">
                            <div class="range-values">
                                <span>0</span>
                                <span id="rangeValue">50</span>
                                <span>100</span>
                            </div>
                        </div>
                        <div class="code-block mt-2">
                            &lt;input type="range" class="form-range"&gt;
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-upload"></i>
                            File Upload
                        </label>
                        <div class="file-upload-wrapper" onclick="document.getElementById('demoFile').click()">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <div>Click to upload or drag and drop</div>
                            <div class="file-info">PNG, JPG, PDF up to 10MB</div>
                            <input type="file" id="demoFile" style="display: none;" multiple>
                        </div>
                        <div class="code-block mt-2">
                            &lt;div class="file-upload-wrapper"&gt;<br>
                            &nbsp;&nbsp;&lt;i class="fas fa-cloud-upload-alt"&gt;&lt;/i&gt;<br>
                            &nbsp;&nbsp;&lt;div&gt;Click to upload&lt;/div&gt;<br>
                            &lt;/div&gt;
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-toggle-on"></i>
                            Toggle Switch
                        </label>
                        <div>
                            <label class="toggle-switch">
                                <input type="checkbox" checked>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <div class="code-block mt-2">
                            &lt;label class="toggle-switch"&gt;<br>
                            &nbsp;&nbsp;&lt;input type="checkbox"&gt;<br>
                            &nbsp;&nbsp;&lt;span class="toggle-slider"&gt;&lt;/span&gt;<br>
                            &lt;/label&gt;
                        </div>
                    </div>
                </div>

                <!-- Row 4: Radio Buttons -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-dot-circle"></i>
                            Radio Buttons
                        </label>
                        <div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="demoRadio" id="demoRadio1" checked>
                                <label class="form-check-label" for="demoRadio1">Option 1</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="demoRadio" id="demoRadio2">
                                <label class="form-check-label" for="demoRadio2">Option 2</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="demoRadio" id="demoRadio3">
                                <label class="form-check-label" for="demoRadio3">Option 3</label>
                            </div>
                        </div>
                        <div class="code-block mt-2">
                            &lt;div class="form-check"&gt;<br>
                            &nbsp;&nbsp;&lt;input type="radio" class="form-check-input"&gt;<br>
                            &nbsp;&nbsp;&lt;label class="form-check-label"&gt;&lt;/label&gt;<br>
                            &lt;/div&gt;
                        </div>
                    </div>

                    <div class="form-group">
                        <!-- Kosong untuk menjaga layout -->
                    </div>

                    <div class="form-group">
                        <!-- Kosong untuk menjaga layout -->
                    </div>
                </div>
            </div>
        </div>

        <!-- INPUT GROUPS TAB -->
        <div class="tab-pane fade" id="inputgroup" role="tabpanel">
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-object-group"></i>
                    Input Groups
                    <span class="ms-auto badge bg-gradient-2">With Icons & Addons</span>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-user"></i>
                            Input with Icon (Left)
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" placeholder="Username">
                        </div>
                        <div class="code-block mt-2">
                            &lt;div class="input-group"&gt;<br>
                            &nbsp;&nbsp;&lt;span class="input-group-text"&gt;&lt;i class="fas fa-user"&gt;&lt;/i&gt;&lt;/span&gt;<br>
                            &nbsp;&nbsp;&lt;input type="text" class="form-control" placeholder="Username"&gt;<br>
                            &lt;/div&gt;
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-tag"></i>
                            Input with Icon (Right)
                        </label>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search categories">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                        <div class="code-block mt-2">
                            &lt;div class="input-group"&gt;<br>
                            &nbsp;&nbsp;&lt;input type="text" class="form-control" placeholder="Search"&gt;<br>
                            &nbsp;&nbsp;&lt;span class="input-group-text"&gt;&lt;i class="fas fa-search"&gt;&lt;/i&gt;&lt;/span&gt;<br>
                            &lt;/div&gt;
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-truck"></i>
                            Input with Icon (Both Sides)
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-truck"></i></span>
                            <input type="text" class="form-control" placeholder="Supplier name">
                            <span class="input-group-text"><i class="fas fa-check"></i></span>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-dollar-sign"></i>
                            Currency Input
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" placeholder="0.00" step="0.01" value="0.00">
                            <span class="input-group-text">.00</span>
                        </div>
                        <div class="code-block mt-2">
                            &lt;div class="input-group"&gt;<br>
                            &nbsp;&nbsp;&lt;span class="input-group-text"&gt;$&lt;/span&gt;<br>
                            &nbsp;&nbsp;&lt;input type="number" class="form-control" step="0.01"&gt;<br>
                            &nbsp;&nbsp;&lt;span class="input-group-text"&gt;.00&lt;/span&gt;<br>
                            &lt;/div&gt;
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-percent"></i>
                            Percentage Input
                        </label>
                        <div class="input-group">
                            <input type="number" class="form-control" placeholder="0" step="0.1" value="0">
                            <span class="input-group-text">%</span>
                        </div>
                        <div class="code-block mt-2">
                            &lt;div class="input-group"&gt;<br>
                            &nbsp;&nbsp;&lt;input type="number" class="form-control"&gt;<br>
                            &nbsp;&nbsp;&lt;span class="input-group-text"&gt;%&lt;/span&gt;<br>
                            &lt;/div&gt;
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-at"></i>
                            Email Input Group
                        </label>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="username">
                            <span class="input-group-text">@</span>
                            <input type="text" class="form-control" placeholder="domain.com">
                        </div>
                        <div class="code-block mt-2">
                            &lt;div class="input-group"&gt;<br>
                            &nbsp;&nbsp;&lt;input type="text" placeholder="username"&gt;<br>
                            &nbsp;&nbsp;&lt;span class="input-group-text"&gt;@&lt;/span&gt;<br>
                            &nbsp;&nbsp;&lt;input type="text" placeholder="domain.com"&gt;<br>
                            &lt;/div&gt;
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-clock"></i>
                            DateTime Group
                        </label>
                        <div class="input-group">
                            <input type="date" class="form-control">
                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                            <input type="time" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-weight"></i>
                            Weight Input
                        </label>
                        <div class="input-group">
                            <input type="number" class="form-control" placeholder="0" step="0.1" value="0">
                            <span class="input-group-text">kg</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-globe"></i>
                            Website Input
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">https://</span>
                            <input type="text" class="form-control" placeholder="example.com">
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-phone"></i>
                            Phone with Country Code
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">+62</span>
                            <input type="tel" class="form-control" placeholder="812-3456-7890">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-search"></i>
                            Search with Button
                        </label>
                        <div class="input-group">
                            <input type="search" class="form-control" placeholder="Search products...">
                            <button class="btn btn-primary" type="button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-font"></i>
                            Text with Prefix/Suffix
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">Prefix</span>
                            <input type="text" class="form-control" placeholder="Text here">
                            <span class="input-group-text">Suffix</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- VALIDATION TAB -->
        <div class="tab-pane fade" id="validation" role="tabpanel">
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-check-circle"></i>
                    Form Validation Examples
                    <span class="ms-auto badge bg-gradient-2">With Validation States</span>
                </div>

                <!-- Row 1: Basic Validation -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-user"></i>
                            Required Field (Valid)
                        </label>
                        <input type="text" class="form-control is-valid" placeholder="Valid input" value="John Doe">
                        <div class="valid-feedback">
                            Looks good!
                        </div>
                        <div class="code-block mt-2">
                            &lt;input type="text" class="form-control is-valid"&gt;<br>
                            &lt;div class="valid-feedback"&gt;Looks good!&lt;/div&gt;
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-exclamation-triangle"></i>
                            Required Field (Invalid)
                        </label>
                        <input type="text" class="form-control is-invalid" placeholder="Invalid input" value="wrong@">
                        <div class="invalid-feedback">
                            Please enter a valid email address.
                        </div>
                        <div class="code-block mt-2">
                            &lt;input type="text" class="form-control is-invalid"&gt;<br>
                            &lt;div class="invalid-feedback"&gt;Error message&lt;/div&gt;
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-envelope"></i>
                            Email Validation (Live)
                        </label>
                        <input type="email" class="form-control" placeholder="Enter email" id="validationEmail">
                        <div class="invalid-feedback" id="emailFeedback">
                            Please provide a valid email.
                        </div>
                        <small class="text-muted">Try: test@example.com</small>
                    </div>
                </div>

                <!-- Row 2: Length & Pattern Validation -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-ruler"></i>
                            Min Length (5 chars)
                        </label>
                        <input type="text" class="form-control" placeholder="Min 5 characters" id="minLengthInput">
                        <div class="invalid-feedback">
                            Must be at least 5 characters.
                        </div>
                        <div class="code-block mt-2">
                            &lt;input type="text" minlength="5"&gt;
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-ruler"></i>
                            Max Length (10 chars)
                        </label>
                        <input type="text" class="form-control" placeholder="Max 10 characters" id="maxLengthInput" maxlength="10">
                        <div class="invalid-feedback">
                            Maximum 10 characters only.
                        </div>
                        <div class="code-block mt-2">
                            &lt;input type="text" maxlength="10"&gt;
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-font"></i>
                            Only Letters
                        </label>
                        <input type="text" class="form-control" placeholder="Letters only" id="lettersOnly">
                        <div class="invalid-feedback">
                            Please enter letters only.
                        </div>
                        <div class="code-block mt-2">
                            &lt;input type="text" pattern="[A-Za-z]+"&gt;
                        </div>
                    </div>
                </div>

                <!-- Row 3: Number Validation -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-hashtag"></i>
                            Number Range (1-100)
                        </label>
                        <input type="number" class="form-control" placeholder="1-100" min="1" max="100" id="rangeNumber">
                        <div class="invalid-feedback">
                            Please enter a number between 1 and 100.
                        </div>
                        <div class="code-block mt-2">
                            &lt;input type="number" min="1" max="100"&gt;
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-percent"></i>
                            Percentage (0-100)
                        </label>
                        <input type="number" class="form-control" placeholder="0-100" min="0" max="100" step="1" id="percentage">
                        <div class="invalid-feedback">
                            Percentage must be between 0 and 100.
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-dollar-sign"></i>
                            Positive Number
                        </label>
                        <input type="number" class="form-control" placeholder="Positive only" min="0" id="positiveNumber">
                        <div class="invalid-feedback">
                            Please enter a positive number.
                        </div>
                    </div>
                </div>

                <!-- Row 4: Password & Match Validation -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-lock"></i>
                            Password (min 8 chars)
                        </label>
                        <input type="password" class="form-control" placeholder="Password" id="password" minlength="8">
                        <div class="invalid-feedback">
                            Password must be at least 8 characters.
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-lock"></i>
                            Confirm Password
                        </label>
                        <input type="password" class="form-control" placeholder="Confirm password" id="confirmPassword">
                        <div class="invalid-feedback" id="passwordMatchFeedback">
                            Passwords do not match.
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-check-circle"></i>
                            Must Agree
                        </label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="agreeCheck" required>
                            <label class="form-check-label" for="agreeCheck">
                                I agree to the terms
                            </label>
                            <div class="invalid-feedback">
                                You must agree before submitting.
                            </div>
                        </div>
                        <div class="code-block mt-2">
                            &lt;input type="checkbox" required&gt;
                        </div>
                    </div>
                </div>

                <!-- Row 5: Custom Validation -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-phone"></i>
                            Phone Indonesia
                        </label>
                        <input type="tel" class="form-control" placeholder="0812-3456-7890" id="phoneIndonesia">
                        <div class="invalid-feedback">
                            Format: 0812-3456-7890
                        </div>
                        <div class="code-block mt-2">
                            &lt;input pattern="[0-9]{4}-[0-9]{4}-[0-9]{4}"&gt;
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-globe"></i>
                            URL Validation
                        </label>
                        <input type="url" class="form-control" placeholder="https://example.com" id="urlInput">
                        <div class="invalid-feedback">
                            Please enter a valid URL.
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-calendar"></i>
                            Date Validation (2024)
                        </label>
                        <input type="date" class="form-control" id="dateInput" min="2024-01-01" max="2024-12-31">
                        <div class="invalid-feedback">
                            Please select a date in 2024.
                        </div>
                    </div>
                </div>

                <!-- Row 6: Username Check & File -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-server"></i>
                            Username Check
                        </label>
                        <input type="text" class="form-control" placeholder="Check username" id="usernameCheck">
                        <div class="valid-feedback" id="usernameValid" style="display: none;">
                            Username is available!
                        </div>
                        <div class="invalid-feedback" id="usernameInvalid" style="display: none;">
                            Username already taken!
                        </div>
                        <small>Try: "admin" (taken) or "user123" (available)</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-file"></i>
                            File Type Validation
                        </label>
                        <input type="file" class="form-control" id="fileInput" accept=".jpg,.jpeg,.png,.pdf">
                        <div class="invalid-feedback">
                            Only JPG, PNG, or PDF files are allowed.
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-list"></i>
                            Required Select
                        </label>
                        <select class="form-control" id="requiredSelect" required>
                            <option value="">-- Choose an option --</option>
                            <option value="1">Option 1</option>
                            <option value="2">Option 2</option>
                            <option value="3">Option 3</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select an option.
                        </div>
                    </div>
                </div>

                <!-- Live Validation Demo Form -->
                <div class="form-section mt-4" style="border: 2px solid var(--accent-3);">
                    <div class="form-section-title">
                        <i class="fas fa-play-circle"></i>
                        Live Validation Demo Form
                        <span class="ms-auto badge bg-gradient-2">Try it!</span>
                    </div>

                    <form id="demoValidationForm" class="needs-validation" novalidate>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Full Name *</label>
                                <input type="text" class="form-control" required pattern="[A-Za-z ]+" minlength="3">
                                <div class="invalid-feedback">
                                    Enter your full name (letters only, min 3 chars).
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Email *</label>
                                <input type="email" class="form-control" required>
                                <div class="invalid-feedback">
                                    Please provide a valid email.
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Age *</label>
                                <input type="number" class="form-control" required min="18" max="100">
                                <div class="invalid-feedback">
                                    You must be at least 18 years old.
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Phone</label>
                                <input type="tel" class="form-control" pattern="[0-9]{10,13}" placeholder="081234567890">
                                <div class="invalid-feedback">
                                    Enter a valid phone number (10-13 digits).
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Gender *</label>
                                <select class="form-control" required>
                                    <option value="">-- Select Gender --</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                                <div class="invalid-feedback">
                                    Please select your gender.
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Terms *</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" required id="termsCheck">
                                    <label class="form-check-label" for="termsCheck">
                                        I accept the terms
                                    </label>
                                    <div class="invalid-feedback">
                                        You must agree before submitting.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button class="btn btn-primary" type="submit" id="submitDemoForm">
                            <i class="fas fa-paper-plane me-2"></i>Submit Form
                        </button>
                        <button class="btn btn-secondary" type="reset" id="resetDemoForm">
                            <i class="fas fa-undo me-2"></i>Reset
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Code Modal -->
<div class="modal fade" id="codeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-code me-2" style="color: var(--accent-3);"></i>
                    Complete HTML Structure
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <pre class="code-block" style="max-height: 500px; overflow-y: auto;">
&lt;!-- Basic Input --&gt;
&lt;div class="form-group"&gt;
    &lt;label class="form-label"&gt;
        &lt;i class="fas fa-user"&gt;&lt;/i&gt;
        Text Input
    &lt;/label&gt;
    &lt;input type="text" class="form-control" placeholder="Enter text..."&gt;
&lt;/div&gt;

&lt;!-- Double Input --&gt;
&lt;div class="double-input"&gt;
    &lt;div class="form-group"&gt;
        &lt;label&gt;First Name&lt;/label&gt;
        &lt;input type="text" class="form-control"&gt;
    &lt;/div&gt;
    &lt;div class="form-group"&gt;
        &lt;label&gt;Last Name&lt;/label&gt;
        &lt;input type="text" class="form-control"&gt;
    &lt;/div&gt;
&lt;/div&gt;

&lt;!-- Dynamic Input --&gt;
&lt;div class="dynamic-input-wrapper"&gt;
    &lt;div id="dynamicContainer"&gt;
        &lt;div class="dynamic-item"&gt;
            &lt;input type="text" class="form-control"&gt;
            &lt;button class="btn-remove-dynamic"&gt;&lt;i class="fas fa-times"&gt;&lt;/i&gt;&lt;/button&gt;
        &lt;/div&gt;
    &lt;/div&gt;
    &lt;button class="btn-add-dynamic"&gt;&lt;i class="fas fa-plus"&gt;&lt;/i&gt; Add&lt;/button&gt;
&lt;/div&gt;

&lt;!-- Input Group --&gt;
&lt;div class="input-group"&gt;
    &lt;span class="input-group-text"&gt;&lt;i class="fas fa-user"&gt;&lt;/i&gt;&lt;/span&gt;
    &lt;input type="text" class="form-control" placeholder="Username"&gt;
&lt;/div&gt;

&lt;!-- Advanced with Flatpickr --&gt;
&lt;input type="text" class="form-control datepicker" placeholder="Select date"&gt;

&lt;!-- Advanced with Select2 --&gt;
&lt;select class="form-control select2-single"&gt;
    &lt;option value=""&gt;-- Select --&lt;/option&gt;
    &lt;option value="1"&gt;Option 1&lt;/option&gt;
&lt;/select&gt;

&lt;!-- Toggle Switch --&gt;
&lt;label class="toggle-switch"&gt;
    &lt;input type="checkbox"&gt;
    &lt;span class="toggle-slider"&gt;&lt;/span&gt;
&lt;/label&gt;

&lt;!-- File Upload --&gt;
&lt;div class="file-upload-wrapper" onclick="document.getElementById('file').click()"&gt;
    &lt;i class="fas fa-cloud-upload-alt"&gt;&lt;/i&gt;
    &lt;div&gt;Click to upload&lt;/div&gt;
    &lt;input type="file" id="file" style="display: none;"&gt;
&lt;/div&gt;
                </pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="copyCode()">
                    <i class="fas fa-copy me-2"></i>Copy Code
                </button>
                <button type="button" class="btn btn-preview" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>
<?php include '../layout/scripts.php'; ?>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    /**
     * script.js - Premium Version
     * Sudah direfactor berdasarkan kritik:
     * 1. DRY (No Copy-Paste)
     * 2. No inline styling
     * 3. Selektor spesifik
     * 4. No setTimeout barbar
     * 5. Clipboard aman
     * 6. HTML Template
     */

    $(document).ready(function() {
        // =============================================
        // 1. FLATPICKR - DRY APPROACH
        // =============================================

        /**
         * Factory function untuk Flatpickr dengan tombol Clear
         */
        function createFlatpickrWithClear(baseConfig) {
            return {
                ...baseConfig,
                allowInput: true,
                onReady: function(selectedDates, dateStr, instance) {
                    attachClearButton(instance);
                }
            };
        }

        /**
         * Helper untuk attach tombol Clear ke instance Flatpickr
         */
        function attachClearButton(instance) {
            // Cegah double button
            if (instance.calendarContainer.querySelector('.flatpickr-clear-btn')) {
                return;
            }

            const clearButton = document.createElement('button');
            clearButton.innerHTML = 'Clear';
            clearButton.className = 'flatpickr-clear-btn'; // Styling di CSS
            clearButton.type = 'button';

            clearButton.addEventListener('click', () => {
                instance.clear();
                instance.close();
            });

            instance.calendarContainer.appendChild(clearButton);
        }

        // Inisialisasi semua Flatpickr dengan 4 baris kode!
        $('.demo-datepicker').flatpickr(createFlatpickrWithClear({
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "F j, Y",
            theme: "material_blue"
        }));

        $('.demo-daterangepicker').flatpickr(createFlatpickrWithClear({
            mode: "range",
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "F j, Y"
        }));

        $('.demo-timepicker').flatpickr(createFlatpickrWithClear({
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true
        }));

        $('.demo-datetimepicker').flatpickr(createFlatpickrWithClear({
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            altInput: true,
            altFormat: "F j, Y H:i",
            time_24hr: true,
            theme: "material_blue"
        }));

        // =============================================
        // 2. SELECT2 - INITIALIZATION
        // =============================================

        /**
         * Generic function untuk inisialisasi Select2
         */
        function initSelect2(selector, placeholder = 'Select an option') {
            $(selector).select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: placeholder,
                allowClear: true,
                dropdownParent: $('body') // Anti kepotong!
            });
        }

        // Init Select2 untuk semua dropdown
        initSelect2('.demo-select2-single', 'Select an option');
        initSelect2('.demo-select2-multiple', 'Select options');
        initSelect2('.demo-select2-department', '-- Select --');

        // =============================================
        // 3. TAB HANDLING - NO MORE SETTIMEOUT!
        // =============================================

        /**
         * Handle tab change dengan event bawaan Bootstrap
         * Spesifik selector: hanya tab di dalam #pills-tabForm
         */
        $('#pills-tabForm button[data-bs-toggle="pill"]').on('shown.bs.tab', function(e) {
            const targetId = $(e.target).attr('data-bs-target');

            // Trigger reposition untuk semua Select2 di tab yang aktif
            $(targetId).find('select[data-select2-id]').each(function() {
                try {
                    // Trik untuk memaksa Select2 menghitung ulang posisi
                    $(this).select2('close');
                    $(this).select2('open');
                    $(this).select2('close');
                } catch (error) {
                    // Ignore error kalo instance belum ada
                    console.debug('Select2 reposition skipped');
                }
            });
        });

        // =============================================
        // 4. DYNAMIC ITEMS - USING HTML TEMPLATE
        // =============================================

        /**
         * Generic function untuk add item dari template
         */
        function addDynamicItem(containerId, templateId) {
            const container = document.getElementById(containerId);
            const template = document.getElementById(templateId);

            if (!container || !template) return;

            // Clone template content
            const newItem = template.content.cloneNode(true);

            // Remove hidden class if exists
            const itemDiv = newItem.querySelector('.dynamic-item');
            if (itemDiv) {
                itemDiv.classList.remove('d-none');
            }

            container.appendChild(newItem);
        }

        // Remove item function (global untuk onclick)
        window.demoRemoveItem = function(btn) {
            const item = $(btn).closest('.dynamic-item');
            const container = item.parent();

            if (container.children('.dynamic-item').length > 1) {
                item.remove();
            } else {
                showFeedback('Minimum one item is required!', 'warning');
            }
        };

        // Event listeners untuk tombol Add
        $('#btnAddPhone').click(() => addDynamicItem('demoPhoneContainer', 'template-phone-item'));
        $('#btnAddSkill').click(() => addDynamicItem('demoSkillContainer', 'template-skill-item'));
        $('#btnAddExperience').click(() => addDynamicItem('demoExpContainer', 'template-exp-item'));

        // =============================================
        // 5. CLIPBOARD - AMAN DARI XSS
        // =============================================

        /**
         * Copy code dengan aman (pake textContent, bukan innerHTML)
         */
        window.copyCode = function() {
            const codeElement = document.querySelector('#codeModal pre');

            if (!codeElement) return;

            // AMAN: textContent otomatis escape HTML
            const code = codeElement.textContent || codeElement.innerText;

            navigator.clipboard.writeText(code).then(function() {
                showFeedback('Code copied to clipboard!', 'success');
            }).catch(function(err) {
                console.error('Copy failed:', err);
                showFeedback('Failed to copy code!', 'danger');
            });
        };

        /**
         * Feedback system (bisa upgrade ke toast nanti)
         */
        function showFeedback(message, type = 'success') {
            // Untuk demo pake alert dulu
            alert(message);

            /* Kalo mau pake toast Bootstrap nanti:
            const toast = new bootstrap.Toast(document.getElementById('feedbackToast'));
            $('#toastMessage').text(message);
            toast.show();
            */
        }

        // =============================================
        // 6. RANGE SLIDER
        // =============================================

        window.updateRangeValue = function(val) {
            document.getElementById('rangeValue').textContent = val;
        };

        // =============================================
        // 7. MODAL HANDLING
        // =============================================

        $('#showCodeBtn').click(function() {
            $('#codeModal').modal('show');
        });

        // =============================================
        // 8. BOOTSTRAP VALIDATION
        // =============================================

        (function() {
            'use strict';

            const forms = document.querySelectorAll('.needs-validation');

            Array.from(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    } else {
                        event.preventDefault();
                        showFeedback('Form submitted successfully! (Demo only)', 'success');
                    }

                    form.classList.add('was-validated');
                }, false);
            });
        })();

        // =============================================
        // 9. VALIDATIONS - SEMUA VALIDASI FORM
        // =============================================

        // Username check
        $('#usernameCheck').on('input', function() {
            const username = $(this).val();
            const takenUsernames = ['admin', 'administrator', 'root', 'user'];

            $(this).removeClass('is-valid is-invalid');
            $('#usernameValid, #usernameInvalid').hide();

            if (username.length > 0) {
                if (takenUsernames.includes(username.toLowerCase())) {
                    $(this).addClass('is-invalid');
                    $('#usernameInvalid').show();
                } else {
                    $(this).addClass('is-valid');
                    $('#usernameValid').show();
                }
            }
        });

        // Password match
        $('#password, #confirmPassword').on('keyup', function() {
            const password = $('#password').val();
            const confirm = $('#confirmPassword').val();

            if (confirm.length > 0) {
                if (password === confirm) {
                    $('#confirmPassword').removeClass('is-invalid').addClass('is-valid');
                    $('#passwordMatchFeedback').hide();
                } else {
                    $('#confirmPassword').removeClass('is-valid').addClass('is-invalid');
                    $('#passwordMatchFeedback').show();
                }
            } else {
                $('#confirmPassword').removeClass('is-valid is-invalid');
            }
        });

        // Email validation
        $('#validationEmail').on('input', function() {
            const email = $(this).val();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (email.length > 0) {
                if (emailRegex.test(email)) {
                    $(this).removeClass('is-invalid').addClass('is-valid');
                    $('#emailFeedback').hide();
                } else {
                    $(this).removeClass('is-valid').addClass('is-invalid');
                    $('#emailFeedback').show();
                }
            } else {
                $(this).removeClass('is-valid is-invalid');
            }
        });

        // Min length validation
        $('#minLengthInput').on('input', function() {
            if ($(this).val().length >= 5) {
                $(this).removeClass('is-invalid').addClass('is-valid');
            } else if ($(this).val().length > 0) {
                $(this).removeClass('is-valid').addClass('is-invalid');
            } else {
                $(this).removeClass('is-valid is-invalid');
            }
        });

        // Letters only
        $('#lettersOnly').on('input', function() {
            const lettersRegex = /^[A-Za-z]+$/;
            const value = $(this).val();

            if (value.length > 0) {
                if (lettersRegex.test(value)) {
                    $(this).removeClass('is-invalid').addClass('is-valid');
                } else {
                    $(this).removeClass('is-valid').addClass('is-invalid');
                }
            } else {
                $(this).removeClass('is-valid is-invalid');
            }
        });

        // Phone Indonesia (format: 0812-3456-7890)
        $('#phoneIndonesia').on('input', function() {
            const phoneRegex = /^[0-9]{4}-[0-9]{4}-[0-9]{4}$/;
            const value = $(this).val();

            if (value.length > 0) {
                if (phoneRegex.test(value)) {
                    $(this).removeClass('is-invalid').addClass('is-valid');
                } else {
                    $(this).removeClass('is-valid').addClass('is-invalid');
                }
            } else {
                $(this).removeClass('is-valid is-invalid');
            }
        });

        // Range number (1-100)
        $('#rangeNumber').on('input', function() {
            const value = parseInt($(this).val());

            if (!isNaN(value) && value >= 1 && value <= 100) {
                $(this).removeClass('is-invalid').addClass('is-valid');
            } else if ($(this).val().length > 0) {
                $(this).removeClass('is-valid').addClass('is-invalid');
            } else {
                $(this).removeClass('is-valid is-invalid');
            }
        });

        // Positive number
        $('#positiveNumber').on('input', function() {
            const value = parseInt($(this).val());

            if (!isNaN(value) && value >= 0) {
                $(this).removeClass('is-invalid').addClass('is-valid');
            } else if ($(this).val().length > 0) {
                $(this).removeClass('is-valid').addClass('is-invalid');
            } else {
                $(this).removeClass('is-valid is-invalid');
            }
        });

        // =============================================
        // 10. UTILITY - AUTO FORMAT PHONE (Bonus)
        // =============================================

        $('#phoneIndonesia').on('keyup', function(e) {
            // Auto format 081234567890 -> 0812-3456-7890
            if (e.key !== 'Backspace' && e.key !== 'Delete') {
                let value = $(this).val().replace(/\D/g, '');

                if (value.length > 4) {
                    value = value.substring(0, 4) + '-' + value.substring(4);
                }
                if (value.length > 9) {
                    value = value.substring(0, 9) + '-' + value.substring(9, 13);
                }

                $(this).val(value);
            }
        });

        // Log sukses
        console.log('✅ Script loaded successfully - Premium Version');
    });
</script>