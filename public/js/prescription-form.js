/**
 * prescription-form.js
 * 2 ô Select2 AJAX:
 *   1. Nhóm thuốc  → medicine_categories.ajax.search  (window.CATEGORY_AJAX_URL)
 *   2. Tên thuốc   → medicines.ajax.search             (window.MEDICINE_AJAX_URL)
 * Khi chọn nhóm → tự động reset ô thuốc và filter AJAX theo nhóm đó.
 */

let cardCount = 0;

function buildMedicineCard(opts) {
    opts = opts || {};
    const idx      = cardCount++;
    const unit     = opts.unit || '';
    const medId    = opts.id || '';
    const medName  = opts.name || '';
    const catId    = opts.category_id || '';
    const catName  = opts.category_name || '';
    const duration = opts.duration_days || '';
    const quantity = opts.quantity || '';
    const dosage   = opts.dosage || '';
    const usageRaw = opts.usage_instructions || '';

    let timing = '';
    if (/trước ăn/i.test(usageRaw))             timing = 'before';
    else if (/trong (khi )?ăn/i.test(usageRaw)) timing = 'during';
    else if (/sau ăn/i.test(usageRaw))          timing = 'after';
    const extraNote = usageRaw
        .replace(/(trước ăn|trong khi ăn|trong ăn|sau ăn)/gi, '')
        .trim().replace(/^[,.\-\s]+/, '');

    const timingBefore = timing === 'before' ? 'btn-warning active' : 'btn-outline-warning';
    const timingDuring = timing === 'during' ? 'btn-info active'    : 'btn-outline-info';
    const timingAfter  = timing === 'after'  ? 'btn-success active' : 'btn-outline-success';

    const card = document.createElement('div');
    card.className = 'rx-card mb-2 border rounded bg-white';
    card.dataset.cardIndex = idx;

    card.innerHTML = `
    <div class="rx-row d-flex align-items-end gap-2 px-2 py-2 flex-wrap">

      <!-- STT -->
      <div class="d-flex flex-column align-items-center mb-1" style="flex-shrink:0;">
        <span class="rx-num badge bg-secondary" style="min-width:22px;">${idx + 1}</span>
      </div>

      <!-- Nhóm thuốc -->
      <div style="width:140px;flex-shrink:0;">
        <div class="rx-label">Nhóm thuốc</div>
        <select class="form-select form-select-sm category-select">
          ${catId ? `<option value="${catId}" selected>${catName}</option>` : ''}
        </select>
      </div>

      <!-- Tên thuốc -->
      <div class="rx-med" style="min-width:180px;flex:1 1 180px;">
        <div class="rx-label">Tên thuốc <span class="text-danger">*</span></div>
        <select name="medicines[]" form="examineForm" class="form-select form-select-sm medicine-select" required>
          ${medId ? `<option value="${medId}" selected>${medName}</option>` : ''}
        </select>
      </div>

      <!-- Đơn vị -->
      <div style="width:45px;flex-shrink:0;">
        <div class="rx-label text-center">ĐVT</div>
        <input type="text" name="units[]" form="examineForm" class="form-control form-control-sm unit-input text-center px-1"
               readonly placeholder="-" value="${unit}" title="Đơn vị">
      </div>

      <!-- Liều -->
      <div class="d-flex align-items-end gap-1" style="flex-shrink:0;">
        <div class="text-center" style="width:45px;">
          <div class="rx-label">Sáng</div>
          <input type="number" class="form-control form-control-sm text-center dose-sang dose-input px-0" min="0" step="0.5" placeholder="0" value="">
        </div>
        <div class="text-center" style="width:45px;">
          <div class="rx-label">Trưa</div>
          <input type="number" class="form-control form-control-sm text-center dose-trua dose-input px-0" min="0" step="0.5" placeholder="0" value="">
        </div>
        <div class="text-center" style="width:45px;">
          <div class="rx-label">Chiều</div>
          <input type="number" class="form-control form-control-sm text-center dose-chieu dose-input px-0" min="0" step="0.5" placeholder="0" value="">
        </div>
        <div class="text-center" style="width:45px;">
          <div class="rx-label">Tối</div>
          <input type="number" class="form-control form-control-sm text-center dose-toi dose-input px-0" min="0" step="0.5" placeholder="0" value="">
        </div>
      </div>

      <!-- Số ngày -->
      <div style="width:50px;flex-shrink:0;">
        <div class="rx-label text-center">Ngày</div>
        <input type="number" name="duration_days[]" form="examineForm" class="form-control form-control-sm text-center duration-days px-0"
               min="1" step="1" placeholder="5" value="${duration}" required>
      </div>

      <!-- Tổng SL -->
      <div style="width:55px;flex-shrink:0;">
        <div class="rx-label text-center">Tổng</div>
        <input type="number" name="quantities[]" form="examineForm" class="form-control form-control-sm text-center total-quantity px-0 fw-bold text-success" readonly required value="${quantity}">
        <input type="hidden" name="dosages[]" form="examineForm" class="dosage-input" value="${dosage}">
      </div>

      <!-- Thời điểm uống -->
      <div style="flex-shrink:0; margin-bottom: 12px;">
        <div class="btn-group btn-group-sm">
          <button type="button" class="btn btn-timing btn-${timingBefore}" data-timing="before">Trước</button>
          <button type="button" class="btn btn-timing btn-${timingDuring}" data-timing="during">Trong</button>
          <button type="button" class="btn btn-timing btn-${timingAfter}"  data-timing="after">Sau</button>
        </div>
      </div>

      <!-- Ghi chú -->
      <div style="min-width:110px;flex:1 1 110px;">
        <div class="rx-label">Ghi chú thêm</div>
        <input type="text" class="form-control form-control-sm extra-note"
               placeholder="VD: cách xa sữa..." value="${extraNote}" style="margin-bottom: 12px;">
        <input type="hidden" name="usage_instructions[]" form="examineForm" class="usage-instructions-hidden" value="${usageRaw}">
      </div>

      <!-- Xóa -->
      <div style="flex-shrink:0; margin-bottom: 12px;">
        <button type="button" class="btn btn-sm btn-outline-danger remove-card" title="Xóa dòng">✕</button>
      </div>

    </div>`;

    return card;
}

function recalcCard(card) {
    const sang  = parseFloat(card.querySelector('.dose-sang').value)  || 0;
    const trua  = parseFloat(card.querySelector('.dose-trua').value)  || 0;
    const chieu = parseFloat(card.querySelector('.dose-chieu').value) || 0;
    const toi   = parseFloat(card.querySelector('.dose-toi').value)   || 0;
    const days  = parseFloat(card.querySelector('.duration-days').value) || 0;
    const unit  = card.querySelector('.unit-input').value || 'viên';

    const perDay = sang + trua + chieu + toi;
    const total  = (perDay > 0 && days > 0) ? Math.ceil(perDay * days) : '';

    card.querySelector('.total-quantity').value        = total;

    const parts = [];
    if (sang)  parts.push('Sáng '  + sang);
    if (trua)  parts.push('Trưa '  + trua);
    if (chieu) parts.push('Chiều ' + chieu);
    if (toi)   parts.push('Tối '   + toi);
    card.querySelector('.dosage-input').value = parts.length ? parts.join(', ') + ' ' + unit : '';

    const activeBtn = card.querySelector('.btn-timing.active');
    let timingStr = '';
    if (activeBtn) {
        const t = activeBtn.dataset.timing;
        if (t === 'before')      timingStr = 'Trước ăn';
        else if (t === 'during') timingStr = 'Trong khi ăn';
        else                     timingStr = 'Sau ăn';
    }
    const note = (card.querySelector('.extra-note').value || '').trim();
    card.querySelector('.usage-instructions-hidden').value = [timingStr, note].filter(Boolean).join(', ');
}

function initSelects(card) {
    const catUrl = window.CATEGORY_AJAX_URL;
    const medUrl = window.MEDICINE_AJAX_URL;
    if (!medUrl) { console.error('[prescription-form] MEDICINE_AJAX_URL chưa được set!'); return; }

    const $catSel = $(card).find('.category-select');
    const $medSel = $(card).find('.medicine-select');

    // ── 1. Category Select2 AJAX ──
    $catSel.select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Nhóm thuốc...',
        allowClear: true,
        dropdownParent: $('body'),
        ajax: {
            url: catUrl,
            dataType: 'json',
            delay: 250,
            data: function(params) { return { keyword: params.term || '' }; },
            processResults: function(data) {
                return {
                    results: [{ id: '', text: '-- Tất cả --' }].concat(
                        (data || []).map(function(c) {
                            return { id: c.id, text: c.category_name };
                        })
                    )
                };
            }
        }
    }).on('select2:select select2:clear', function() {
        // Reset medicine khi đổi nhóm
        $medSel.val(null).trigger('change');
        card.querySelector('.unit-input').value = '';
        recalcCard(card);
    });

    // ── 2. Medicine Select2 AJAX (lọc theo nhóm đang chọn) ──
    $medSel.select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Chọn thuốc...',
        allowClear: true,
        dropdownParent: $('body'),
        ajax: {
            url: medUrl,
            dataType: 'json',
            delay: 280,
            data: function(params) {
                return {
                    keyword:     params.term || '',
                    category_id: $catSel.val() || ''
                };
            },
            processResults: function(data) {
                return {
                    results: (data || []).map(function(item) {
                        return { id: item.id, text: item.medicine_name, unit: item.unit || '' };
                    })
                };
            }
        },
        templateResult: function(item) {
            if (item.loading) return item.text;
            var unitTxt = item.unit ? ' <span class="text-muted" style="font-size:.75rem;">(' + item.unit + ')</span>' : '';
            return $('<span>' + item.text + unitTxt + '</span>');
        }
    }).on('select2:select', function(e) {
        const data = e.params.data;
        card.querySelector('.unit-input').value        = data.unit || '';
        recalcCard(card);
    });
}

function addMedicineCard(opts) {
    const container = document.getElementById('prescription-cards-container');
    if (!container) return null;

    const card = buildMedicineCard(opts || {});
    container.appendChild(card);
    initSelects(card);

    // Timing buttons
    card.querySelectorAll('.btn-timing').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const t = btn.dataset.timing;
            card.querySelectorAll('.btn-timing').forEach(function(b) {
                const bt   = b.dataset.timing;
                const base = bt === 'before' ? 'warning' : bt === 'during' ? 'info' : 'success';
                b.className = 'btn btn-sm btn-timing btn-outline-' + base;
            });
            const base = t === 'before' ? 'warning' : t === 'during' ? 'info' : 'success';
            btn.className = 'btn btn-sm btn-timing btn-' + base + ' active';
            recalcCard(card);
        });
    });

    card.querySelectorAll('.dose-input, .duration-days, .extra-note, .unit-input').forEach(function(el) {
        el.addEventListener('input', function() { recalcCard(card); });
    });

    card.querySelector('.remove-card').addEventListener('click', function() {
        if (document.querySelectorAll('.rx-card').length > 1) {
            $(card).find('.category-select, .medicine-select').each(function() {
                if ($(this).hasClass('select2-hidden-accessible')) $(this).select2('destroy');
            });
            card.remove();
            updateCardNumbers();
            updateRemoveButtons();
        }
    });

    updateCardNumbers();
    updateRemoveButtons();
    return card;
}

function updateCardNumbers() {
    document.querySelectorAll('.rx-card').forEach(function(card, i) {
        const badge = card.querySelector('.rx-num');
        if (badge) badge.textContent = i + 1;
    });
}

function updateRemoveButtons() {
    const cards = document.querySelectorAll('.rx-card');
    cards.forEach(function(card) {
        const btn = card.querySelector('.remove-card');
        if (btn) btn.disabled = (cards.length === 1);
    });
}

window.applyReuseData = function(data) {
    const container = document.getElementById('prescription-cards-container');
    if (!container) return;
    $(container).find('.category-select, .medicine-select').each(function() {
        if ($(this).hasClass('select2-hidden-accessible')) $(this).select2('destroy');
    });
    container.innerHTML = '';
    cardCount = 0;
    const adviceTA = document.querySelector('textarea[name="doctor_advice"]');
    if (adviceTA && data.advice) adviceTA.value = data.advice;
    (data.medicines || []).forEach(function(med) { addMedicineCard(med); });
    const prescSection = document.getElementById('prescriptionSection');
    if (prescSection) prescSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
};

document.addEventListener('DOMContentLoaded', function() {
    const btnAdd = document.getElementById('btn-add-medicine');
    if (!btnAdd) return;

    addMedicineCard();
    btnAdd.addEventListener('click', function() {
        let opts = {};
        const cards = document.querySelectorAll('.rx-card');
        if (cards.length > 0) {
            const lastCard = cards[cards.length - 1];
            
            // Lấy nhóm thuốc của dòng cuối cùng
            const $catSel = $(lastCard).find('.category-select');
            const catData = $catSel.select2('data');
            if (catData && catData.length > 0 && catData[0].id) {
                opts.category_id = catData[0].id;
                opts.category_name = catData[0].text;
            }

            // Lấy số ngày của dòng cuối cùng
            const duration = lastCard.querySelector('.duration-days').value;
            if (duration) {
                opts.duration_days = duration;
            }
        }
        addMedicineCard(opts);
    });

    const savedRaw = localStorage.getItem('reuse_prescription_data');
    if (savedRaw) {
        try {
            const saved = JSON.parse(savedRaw);
            if (Date.now() - saved.timestamp < 300000) window.applyReuseData(saved);
        } catch(e) {}
        localStorage.removeItem('reuse_prescription_data');
    }

    window.addEventListener('message', function(event) {
        if (event.data && event.data.type === 'REUSE_PRESCRIPTION') window.applyReuseData(event.data);
    });
});
