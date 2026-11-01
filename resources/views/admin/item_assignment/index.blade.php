@extends('admin.layouts.app')
@section('page_title', 'Item Assignment')
@section('content')
<div class="data-card">
    <div class="table-header">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="p-2 rounded" style="background:#eef2ff">
                    <i class="bi bi-link-45deg fs-5" style="color:#6366f1"></i>
                </div>
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <h3 class="card-section-title mb-0">Assignment Matrix</h3>

                </div>
            </div>
            <button onclick="saveAllAssignments()" class="btn btn-primary btn-sm px-4 flex-shrink-0" style="border-radius:.75rem;font-size:.75rem;font-weight:700">
                <i class="bi bi-check-lg me-1"></i> Save Changes
            </button>
        </div>
    </div>
    <div class="overflow-x-auto p-4" id="matrixContainer">
        @include('admin.item_assignment._table')
    </div>
</div>
@endsection
@section('script')
<script>
let currentCompanyId = {{ $currentCompanyId ?? 'null' }};
let pendingAssignments = {};
let matrixUnits = [];

const data = @json($matrixData);

function buildPendingFromSaved(saved) {
    if (!saved || Array.isArray(saved)) return {};
    const map = {};
    Object.keys(saved).forEach(fid => {
        map[fid] = {};
        Object.keys(saved[fid]).forEach(rid => {
            map[fid][rid] = {
                value: saved[fid][rid].value || '',
                unit_name: saved[fid][rid].unit_name || '',
            };
        });
    });
    return map;
}

pendingAssignments = buildPendingFromSaved(data.assignments || {});
matrixUnits = data.units || [];

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

function unitOptions(selectedName) {
    let opts = '';
    matrixUnits.forEach(u => {
        const sel = u.unit_name === selectedName ? 'selected' : '';
        opts += `<option value="${escHtml(u.unit_name)}" ${sel}>${escHtml(u.unit_name)}</option>`;
    });
    return opts;
}

function renderMatrix(finished, raw) {
    const container = document.getElementById('matrixContainer');
    if (!finished.length || !raw.length) {
        container.innerHTML = '<div class="p-5 text-center text-secondary" style="font-size:.875rem">Both finished and raw items are required to show the matrix.</div>';
        return;
    }

    let html = '<div class="table-responsive" style="max-height: 80vh; overflow: auto;"><table class="table table-bordered align-middle mb-0" style="font-size:.8125rem">';
    html += '<thead><tr>';
    html += '<th class="px-3 py-3 text-center" style="width:200px; position: sticky; top: 0; left: 0; z-index: 3; background-color: #fff;">Finished &nbsp;\\&nbsp; Raw</th>';
    raw.forEach(r => {
        html += `<th class="px-3 py-3 text-center text-nowrap" style="position: sticky; top: 0; z-index: 2; background-color: #fff;">
            <span class="fw-bold" style="color:#d97706">${escHtml(r.short_code)}</span>
            <span class="text-secondary ms-1" style="font-size:.6875rem;font-weight:500">${escHtml(r.item_name)}</span>
        </th>`;
    });
    html += '</tr></thead><tbody>';

    finished.forEach(f => {
        if (!pendingAssignments[f.id]) pendingAssignments[f.id] = {};

        html += `<tr>`;
        const finUnit = f.unit_name || '';
        html += `<td class="px-3 py-3" style="position: sticky; left: 0; z-index: 1; background-color: #fff; box-shadow: inset -1px 0 0 rgba(0,0,0,0.1);">
            <label class="d-flex align-items-center gap-2 mb-0" style="cursor:pointer">
                <input type="checkbox" onchange="toggleRowSelect(this, ${f.id})" class="form-check-input mt-0" style="width:16px;height:16px;cursor:pointer">
                <div class="d-flex flex-column">
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-bold text-dark">${escHtml(f.item_name)}</span>
                        <span class="badge bg-light text-success border border-success" style="font-size:.625rem;font-weight:700">${escHtml(f.short_code)}</span>
                    </div>
                    ${finUnit ? `<span class="badge bg-light text-dark border mt-2" style="font-size:.625rem;font-weight:500">Per ${escHtml(finUnit)}</span>` : ''}
                </div>
            </label>
        </td>`;
        raw.forEach(r => {
            const assigned = pendingAssignments[f.id] && pendingAssignments[f.id][r.id];
            const checked = assigned ? 'checked' : '';
            const val = assigned && assigned.value ? assigned.value : '';
            const unitName = assigned && assigned.unit_name ? assigned.unit_name : (r.unit_name || (matrixUnits.length ? matrixUnits[0].unit_name : ''));
            html += `<td class="px-3 py-3 text-center">
                <div class="d-flex flex-column align-items-center gap-1">
                    <input type="checkbox" class="form-check-input mt-0 assign-checkbox" style="width:16px;height:16px;cursor:pointer"
                        data-finished="${f.id}" data-raw="${r.id}" ${checked}
                        onchange="onCheckChange(this)">
                    <div class="d-flex gap-1">
                        <input type="text" class="form-control form-control-sm assign-input text-center" style="width:65px;font-size:.75rem;border-radius:.5rem"
                            placeholder="Qty" data-finished="${f.id}" data-raw="${r.id}" value="${escHtml(val)}"
                            oninput="onInputChange(this)">
                        <select class="form-select form-select-sm assign-unit" style="width:auto;min-width:65px;font-size:.6875rem;border-radius:.5rem"
                            data-finished="${f.id}" data-raw="${r.id}"
                            onchange="onUnitChange(this)">${unitOptions(unitName)}</select>
                    </div>
                </div>
            </td>`;
        });
        html += '</tr>';
    });

    html += '</tbody></table></div>';
    container.innerHTML = html;
}

function escHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function loadMatrix(companyId) {
    currentCompanyId = companyId;
    const url = BASE_URL + '/admin/item-assignment/matrix-data/' + companyId;
    fetch(url, {
        headers: { 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(res => {
        matrixUnits = res.units || [];
        pendingAssignments = buildPendingFromSaved(res.assignments || {});
        renderMatrix(res.finished || [], res.raw || []);
    })
    .catch(() => {
        Swal.fire({ title: 'Error', text: 'Failed to load matrix data.', icon: 'error', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
    });
}

function onCheckChange(el) {
    const finishedId = parseInt(el.dataset.finished);
    const rawId = parseInt(el.dataset.raw);
    if (!pendingAssignments[finishedId]) pendingAssignments[finishedId] = {};
    if (el.checked) {
        if (!pendingAssignments[finishedId][rawId]) {
            const unitEl = document.querySelector(`.assign-unit[data-finished="${finishedId}"][data-raw="${rawId}"]`);
            pendingAssignments[finishedId][rawId] = { value: '', unit_name: unitEl ? unitEl.value : '' };
        }
    } else {
        delete pendingAssignments[finishedId][rawId];
        if (Object.keys(pendingAssignments[finishedId]).length === 0) delete pendingAssignments[finishedId];
    }
}

function onInputChange(el) {
    const finishedId = parseInt(el.dataset.finished);
    const rawId = parseInt(el.dataset.raw);
    if (!pendingAssignments[finishedId]) pendingAssignments[finishedId] = {};
    if (!pendingAssignments[finishedId][rawId]) {
        const unitEl = document.querySelector(`.assign-unit[data-finished="${finishedId}"][data-raw="${rawId}"]`);
        pendingAssignments[finishedId][rawId] = { value: '', unit_name: unitEl ? unitEl.value : '' };
    }
    pendingAssignments[finishedId][rawId].value = el.value;
}

function onUnitChange(el) {
    const finishedId = parseInt(el.dataset.finished);
    const rawId = parseInt(el.dataset.raw);
    if (!pendingAssignments[finishedId]) pendingAssignments[finishedId] = {};
    if (!pendingAssignments[finishedId][rawId]) {
        pendingAssignments[finishedId][rawId] = { value: '', unit_name: '' };
    }
    pendingAssignments[finishedId][rawId].unit_name = el.value;
}

function saveAllAssignments() {
    const payload = {
        company_id: currentCompanyId,
        assignments: pendingAssignments
    };

    fetch(BASE_URL + '/admin/item-assignment/save-all', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
            'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            Swal.fire({ title: 'Saved!', text: 'All assignments have been saved.', icon: 'success', timer: 1500, showConfirmButton: false, toast: true, position: 'top-end' });
        } else {
            Swal.fire({ title: 'Error', text: res.message || 'Failed to save assignments.', icon: 'error', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
        }
    })
    .catch(() => {
        Swal.fire({ title: 'Error', text: 'Something went wrong.', icon: 'error', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
    });
}

function toggleRowSelect(el, finishedId) {
    const checkboxes = document.querySelectorAll(`.assign-checkbox[data-finished="${finishedId}"]`);
    checkboxes.forEach(cb => {
        cb.checked = el.checked;
        const rawId = parseInt(cb.dataset.raw);
        if (!pendingAssignments[finishedId]) pendingAssignments[finishedId] = {};
        if (el.checked) {
            if (!pendingAssignments[finishedId][rawId]) {
                const unitEl = document.querySelector(`.assign-unit[data-finished="${finishedId}"][data-raw="${rawId}"]`);
                pendingAssignments[finishedId][rawId] = { value: '', unit_name: unitEl ? unitEl.value : '' };
            }
        } else {
            delete pendingAssignments[finishedId][rawId];
            if (Object.keys(pendingAssignments[finishedId]).length === 0) delete pendingAssignments[finishedId];
        }
    });
}

renderMatrix(data.finished || [], data.raw || []);
</script>
@endsection
