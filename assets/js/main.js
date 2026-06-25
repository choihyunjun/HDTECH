// ── 토스트 알림 ──
function showToast(msg, type = 'success') {
    const el = document.getElementById('toast');
    el.textContent = msg;
    el.className = 'show ' + type;
    setTimeout(() => { el.className = ''; }, 2800);
}

// ── 숫자 포맷 ──
function fmtNum(n) {
    return parseInt(n || 0).toLocaleString('ko-KR');
}
function parseNum(s) {
    return parseInt((s + '').replace(/,/g, '') || '0');
}

// ── 수리이력 그리드 ──
function addRepairRow(data = {}) {
    const tbody = document.getElementById('repairBody');
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td><input type="date" name="repair_date" value="${data.repair_date || ''}"></td>
        <td><input type="text" name="repair_content" value="${data.content || ''}" placeholder="수리 및 관리 내용"></td>
        <td><input type="text" name="repair_manager" value="${data.manager || ''}" placeholder="담당자" style="width:80px"></td>
        <td><input type="text" name="repair_cost" value="${data.cost ? fmtNum(data.cost) : ''}"
             placeholder="0" style="width:90px;text-align:right"
             oninput="this.value=fmtNum(parseNum(this.value))"></td>
        <td><button type="button" class="del-row-btn no-print" onclick="this.closest('tr').remove()">✕</button></td>
    `;
    tbody.appendChild(tr);
}

function getRepairs() {
    return [...document.querySelectorAll('#repairBody tr')].map(tr => ({
        repair_date: tr.querySelector('[name=repair_date]').value,
        content:     tr.querySelector('[name=repair_content]').value,
        manager:     tr.querySelector('[name=repair_manager]').value,
        cost:        parseNum(tr.querySelector('[name=repair_cost]').value),
    }));
}

// ── 작업이력 그리드 ──
function addWorklogRow(data = {}) {
    const tbody = document.getElementById('worklogBody');
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td><input type="date" name="work_date" value="${data.work_date || ''}"></td>
        <td><input type="text" name="work_equipment" value="${data.equipment || ''}" placeholder="설비명" style="width:80px"></td>
        <td><input type="text" name="work_content" value="${data.content || ''}" placeholder="작업내용"></td>
        <td><input type="text" name="work_note" value="${data.note || ''}" placeholder="비고" style="width:80px"></td>
        <td><input type="number" name="work_count" value="${data.work_count || ''}" placeholder="0"
             style="width:70px;text-align:right" oninput="calcTotal()"></td>
        <td><input type="number" name="total_count" value="${data.total_count || ''}" placeholder="0"
             style="width:80px;text-align:right" readonly></td>
        <td><button type="button" class="del-row-btn no-print" onclick="this.closest('tr').remove();calcTotal()">✕</button></td>
    `;
    tbody.appendChild(tr);
}

function calcTotal() {
    let sum = 0;
    document.querySelectorAll('#worklogBody tr').forEach(tr => {
        const cnt = parseInt(tr.querySelector('[name=work_count]')?.value || 0);
        sum += cnt;
        tr.querySelector('[name=total_count]').value = sum;
    });
    const el = document.getElementById('checkCount');
    if (el) el.value = sum.toLocaleString('ko-KR');
}

function getWorklogs() {
    return [...document.querySelectorAll('#worklogBody tr')].map(tr => ({
        work_date:   tr.querySelector('[name=work_date]').value,
        equipment:   tr.querySelector('[name=work_equipment]').value,
        content:     tr.querySelector('[name=work_content]').value,
        note:        tr.querySelector('[name=work_note]').value,
        work_count:  parseInt(tr.querySelector('[name=work_count]').value || 0),
        total_count: parseInt(tr.querySelector('[name=total_count]').value || 0),
    }));
}

// ── 이미지 업로드 ──
let pendingImages = [];

function initImageUpload(moldId) {
    document.getElementById('imgInput').addEventListener('change', async function () {
        const file = this.files[0];
        if (!file) return;

        if (moldId) {
            const fd = new FormData();
            fd.append('file', file);
            fd.append('mold_id', moldId);
            const res  = await fetch('/mold/api/upload.php', { method: 'POST', body: fd });
            const data = await res.json();
            if (data.success) {
                addImageThumb(data.url, data.id);
                showToast('이미지 업로드 완료');
            } else {
                showToast(data.error || '업로드 실패', 'error');
            }
        } else {
            const url = URL.createObjectURL(file);
            pendingImages.push({ file, url });
            addImageThumb(url, null, pendingImages.length - 1);
        }
        this.value = '';
    });
}

async function uploadPendingImages(moldId) {
    for (const item of pendingImages) {
        const fd = new FormData();
        fd.append('file', item.file);
        fd.append('mold_id', moldId);
        await fetch('/mold/api/upload.php', { method: 'POST', body: fd });
    }
    pendingImages = [];
}

function addImageThumb(url, imgId, pendingIdx) {
    const area = document.getElementById('imageArea');
    const div  = document.createElement('div');
    div.className = 'image-thumb';
    div.dataset.imgId      = imgId || '';
    div.dataset.pendingIdx = pendingIdx !== undefined ? pendingIdx : '';
    div.innerHTML = `
        <img src="${url}" onclick="openImgModal('${url}')" title="클릭하면 크게 봅니다">
        <button class="del-img no-print" onclick="removeImage(this)" title="삭제">✕</button>
    `;
    area.insertBefore(div, area.querySelector('.image-upload-btn'));
}

async function removeImage(btn) {
    const div   = btn.closest('.image-thumb');
    const imgId = div.dataset.imgId;
    if (imgId) {
        await fetch('/mold/api/delete_image.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ id: imgId })
        });
    } else {
        const idx = parseInt(div.dataset.pendingIdx);
        if (!isNaN(idx)) pendingImages[idx] = null;
    }
    div.remove();
}

// ── 이미지 모달 ──
function openImgModal(url) {
    const modal = document.getElementById('imgModal');
    if (!modal) return;
    document.getElementById('modalImg').src = url;
    modal.classList.add('active');
}
function closeImgModal() {
    document.getElementById('imgModal')?.classList.remove('active');
}

// ── 인쇄 ──
function printCard() { window.print(); }
