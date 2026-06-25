<?php
require_once 'config.php';
$pageId    = 'card';
$pageTitle = '금형이력카드';
$headerActions = '
    <a href="/mold/index.php" class="btn-header">목록</a>
    <button class="btn-header" onclick="newCard()">신규 등록</button>
    <button class="btn-header" onclick="printCard()">인쇄 / PDF</button>
    <button class="btn-header edit" id="btnEditInfo" style="display:none" onclick="enableInfoEdit()">기본정보 수정</button>
    <button class="btn-header save" onclick="saveCard()">저장</button>
    <button class="btn-header del" onclick="deleteCard()">삭제</button>
';
require_once 'includes/layout.php';
?>

<div class="card-container">

    <!-- 기본정보 테이블 (8열 균등 구조) -->
    <table class="info-table" id="infoTable">
        <colgroup>
            <col style="width:72px">
            <col>
            <col style="width:72px">
            <col>
            <col style="width:72px">
            <col>
            <col style="width:72px">
            <col>
        </colgroup>
        <!-- 타이틀 + 금형번호 -->
        <tr>
            <td colspan="6" class="card-title-cell">금 형 이 력 카 드</td>
            <th class="mold-no-label" style="width:80px">금 형 번 호</th>
            <td class="mold-no-value" style="width:130px">
                <input type="text" id="moldNo" placeholder="금형번호 입력"
                       style="text-align:center;font-weight:700;color:var(--primary);font-size:13px;width:100%">
                <input type="hidden" id="moldId" value="">
            </td>
        </tr>
        <!-- 고객사 / 차종 / 품번 / 제품명 -->
        <tr>
            <th>고 객 사</th>
            <td><input type="text" id="customer" placeholder="고객사명"></td>
            <th>차 종</th>
            <td><input type="text" id="carModel" placeholder="차종"></td>
            <th>품 번</th>
            <td><input type="text" id="partNo" placeholder="품번"></td>
            <th>제 품 명</th>
            <td><input type="text" id="productName" placeholder="제품명"></td>
        </tr>
        <!-- 금형명 / 주사용설비 / 사용재료 -->
        <tr>
            <th>금 형 명</th>
            <td colspan="3"><input type="text" id="moldName" placeholder="금형명"></td>
            <th>주사용설비</th>
            <td><input type="text" id="mainEquipment" placeholder="예: 35 TON"></td>
            <th>사용재료</th>
            <td><input type="text" id="material" placeholder="예: PS-45"></td>
        </tr>
        <!-- 금형치수 / 금형재질 / 제작근거 -->
        <tr>
            <th>금형치수</th>
            <td colspan="3"><input type="text" id="moldSize" placeholder="예: M4.2×16 105/90-45-24"></td>
            <th>금형재질</th>
            <td><input type="text" id="moldMaterial" placeholder="예: SKD61"></td>
            <th>제작근거</th>
            <td><input type="text" id="basis" placeholder="제작근거"></td>
        </tr>
        <!-- 제작처 / 제작일 / 제작비용 / 폐기일 -->
        <tr>
            <th>제 작 처</th>
            <td><input type="text" id="maker" placeholder="제작처"></td>
            <th>제 작 일</th>
            <td><input type="date" id="madeDate"></td>
            <th>제작비용</th>
            <td><input type="text" id="madeCost" placeholder="0" style="text-align:right"
                       oninput="this.value=fmtNum(parseNum(this.value))"></td>
            <th>폐 기 일</th>
            <td><input type="date" id="expireDate"></td>
        </tr>
        <!-- 교환타수 / 최종촬영 / 점검타수 / 금형등급 -->
        <tr>
            <th>교환타수</th>
            <td><input type="text" id="exchangeCount" placeholder="0" style="text-align:right"
                       oninput="this.value=fmtNum(parseNum(this.value))"></td>
            <th>최종촬영</th>
            <td><input type="date" id="lastPhotoDate"></td>
            <th>점검타수</th>
            <td><input type="text" id="checkCount" placeholder="0" readonly
                       style="background:var(--bg-label);text-align:right;color:var(--text-muted)"></td>
            <th>금형등급</th>
            <td>
                <select id="moldGrade">
                    <option value="">선택</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                </select>
            </td>
        </tr>
    </table>

    <!-- 금형관리내역 -->
    <div class="section-title">【 금 형 관 리 내 역 】</div>

    <div class="two-col">
        <div class="left">
            <table class="grid-table">
                <thead>
                    <tr>
                        <th style="width:100px">수리일자</th>
                        <th>수리 및 관리 내용</th>
                        <th style="width:75px">담당자</th>
                        <th style="width:90px">수리비용</th>
                        <th style="width:28px" class="no-print"></th>
                    </tr>
                </thead>
                <tbody id="repairBody"></tbody>
            </table>
            <button class="add-row-btn no-print" onclick="addRepairRow()">＋ 수리이력 추가</button>
        </div>
        <div>
            <div class="section-title">금형형상 및 도면</div>
            <div class="image-area" id="imageArea">
                <label class="image-upload-btn no-print">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>이미지 추가</span>
                    <input type="file" id="imgInput" accept="image/*" style="display:none" multiple>
                </label>
            </div>
        </div>
    </div>

    <!-- 작업이력 -->
    <div class="section-title">【 작 업 이 력 】</div>
    <table class="grid-table">
        <thead>
            <tr>
                <th style="width:100px">작업일자</th>
                <th style="width:85px">사용설비</th>
                <th>작업내용</th>
                <th style="width:90px">비고</th>
                <th style="width:80px">작업타수</th>
                <th style="width:85px">누적타수</th>
                <th style="width:28px" class="no-print"></th>
            </tr>
        </thead>
        <tbody id="worklogBody"></tbody>
    </table>
    <button class="add-row-btn no-print" onclick="addWorklogRow()">＋ 작업이력 추가</button>

    <!-- 특기사항 -->
    <div class="section-title">특 기 사 항</div>
    <div class="note-area">
        <textarea id="noteText" placeholder="특기사항을 입력하세요..."></textarea>
    </div>

    <div class="card-footer">
        <span id="footerDocNo">PS-0713-02</span>
        <span>HAEDEUK TECH</span>
        <span>A4L (210×297)</span>
    </div>

</div><!-- /card-container -->

<!-- 이미지 모달 -->
<div class="modal-overlay" id="imgModal" onclick="closeImgModal()">
    <div class="modal-box" style="padding:8px">
        <img id="modalImg" src="" alt="금형이미지">
    </div>
</div>

<!-- 삭제 확인 모달 -->
<div class="modal-overlay" id="delModal">
    <div class="modal-box modal-confirm">
        <p>이 금형이력카드를 삭제하시겠습니까?<br><small>삭제 후 복구할 수 없습니다.</small></p>
        <div class="modal-btn-row">
            <button class="modal-btn del" onclick="doDelete()">삭제</button>
            <button class="modal-btn cancel" onclick="document.getElementById('delModal').classList.remove('active')">취소</button>
        </div>
    </div>
</div>

<script src="/mold/assets/js/main.js"></script>
<script>
const UPLOAD_URL = '/uploads/';
const urlParams  = new URLSearchParams(location.search);
const moldId     = parseInt(urlParams.get('id') || '0');
let   isDirty    = false;

// ── 변경감지 시작 (데이터 로드 완료 후 호출) ──
function startDirtyTracking() {
    document.querySelectorAll('.card-container input:not([type=hidden]), .card-container select, .card-container textarea')
        .forEach(el => el.addEventListener('input', () => { isDirty = true; }));
}

// ── 기본정보 잠금 해제 ──
function enableInfoEdit() {
    document.getElementById('infoTable').classList.remove('info-locked');
    document.getElementById('btnEditInfo').style.display = 'none';
    isDirty = true;
}

window.onload = async function () {
    initImageUpload(moldId || null);
    if (moldId) {
        await loadCard(moldId);
        document.getElementById('infoTable').classList.add('info-locked');
        document.getElementById('btnEditInfo').style.display = '';
    }
    startDirtyTracking();
};

async function loadCard(id) {
    const res = await fetch(`/mold/api/molds.php?action=get&id=${id}`);
    const d   = await res.json();
    if (d.error) { showToast(d.error, 'error'); return; }

    document.getElementById('moldId').value               = d.id;
    document.getElementById('moldNo').value               = d.mold_no;
    document.getElementById('customer').value             = d.customer        || '';
    document.getElementById('carModel').value             = d.car_model       || '';
    document.getElementById('partNo').value               = d.part_no         || '';
    document.getElementById('productName').value          = d.product_name    || '';
    document.getElementById('moldName').value             = d.mold_name       || '';
    document.getElementById('moldSize').value             = d.mold_size       || '';
    document.getElementById('mainEquipment').value        = d.main_equipment  || '';
    document.getElementById('material').value             = d.material        || '';
    document.getElementById('basis').value                = d.basis           || '';
    document.getElementById('moldMaterial').value         = d.mold_material   || '';
    document.getElementById('expireDate').value           = d.expire_date     || '';
    document.getElementById('maker').value                = d.maker           || '';
    document.getElementById('madeDate').value             = d.made_date       || '';
    document.getElementById('madeCost').value             = d.made_cost       ? fmtNum(d.made_cost) : '';
    document.getElementById('exchangeCount').value        = d.exchange_count  ? fmtNum(d.exchange_count) : '';
    document.getElementById('lastPhotoDate').value        = d.last_photo_date || '';
    document.getElementById('checkCount').value           = d.check_count     ? fmtNum(d.check_count) : '';
    document.getElementById('moldGrade').value            = d.mold_grade      || '';
    document.getElementById('noteText').value             = d.note            || '';

    (d.repairs  || []).forEach(r => addRepairRow(r));
    (d.worklogs || []).forEach(w => addWorklogRow(w));
    (d.images   || []).forEach(img => addImageThumb(UPLOAD_URL + img.file_path, img.id));
}

async function saveCard() {
  try {
    const id = parseInt(document.getElementById('moldId').value || '0');
    const payload = {
        id,
        mold_no:         document.getElementById('moldNo').value,
        customer:        document.getElementById('customer').value,
        car_model:       document.getElementById('carModel').value,
        part_no:         document.getElementById('partNo').value,
        product_name:    document.getElementById('productName').value,
        mold_name:       document.getElementById('moldName').value,
        mold_size:       document.getElementById('moldSize').value,
        main_equipment:  document.getElementById('mainEquipment').value,
        material:        document.getElementById('material').value,
        basis:           document.getElementById('basis').value,
        mold_material:   document.getElementById('moldMaterial').value,
        expire_date:     document.getElementById('expireDate').value,
        maker:           document.getElementById('maker').value,
        made_date:       document.getElementById('madeDate').value,
        made_cost:       parseNum(document.getElementById('madeCost').value),
        exchange_count:  parseNum(document.getElementById('exchangeCount').value),
        last_photo_date: document.getElementById('lastPhotoDate').value,
        mold_grade:      document.getElementById('moldGrade').value,
        note:            document.getElementById('noteText').value,
        repairs:         getRepairs(),
        worklogs:        getWorklogs(),
    };
    if (!payload.mold_no) { showToast('금형번호가 없습니다', 'error'); return; }

    const res  = await fetch('/mold/api/molds.php?action=save', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(payload)
    });
    const text = await res.text();
    let data;
    try { data = JSON.parse(text); }
    catch(e) { showToast('서버 응답 오류: ' + text.slice(0,80), 'error'); return; }

    if (data.success) {
        if (!id && data.id) {
            await uploadPendingImages(data.id);
            history.replaceState(null, '', '/mold/card.php?id=' + data.id);
            document.getElementById('moldId').value = data.id;
        }
        isDirty = false;
        document.getElementById('infoTable').classList.add('info-locked');
        document.getElementById('btnEditInfo').style.display = '';
        showToast('저장 되었습니다', 'success');
    } else {
        showToast(data.error || '저장 실패', 'error');
    }
  } catch(e) {
    showToast('오류: ' + e.message, 'error');
  }
}

function newCard() {
    if (isDirty) {
        if (!confirm('작성 중인 내용이 있습니다.\n저장하지 않고 신규 등록 화면으로 이동하시겠습니까?')) return;
    }
    location.href = '/mold/card.php';
}
function printCard() { window.print(); }

function deleteCard() {
    const id = document.getElementById('moldId').value;
    if (!id) { showToast('저장된 카드가 없습니다', 'error'); return; }
    document.getElementById('delModal').classList.add('active');
}
async function doDelete() {
    const id = document.getElementById('moldId').value;
    const res  = await fetch('/mold/api/molds.php?action=delete', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ id: parseInt(id) })
    });
    const data = await res.json();
    document.getElementById('delModal').classList.remove('active');
    if (data.success) {
        isDirty = false;
        showToast('삭제 완료');
        setTimeout(() => { location.href = '/mold/index.php'; }, 700);
    } else {
        showToast(data.error || '삭제 실패', 'error');
    }
}
</script>

<?php require_once 'includes/layout_end.php'; ?>
