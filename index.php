<?php
require_once 'config.php';
$pageId    = 'list';
$pageTitle = '금형 목록';
$headerActions = '
    <a href="/mold/card.php" class="btn-header">＋ 신규 등록</a>
';
require_once 'includes/layout.php';
?>


<!-- 목록 패널 -->
<div class="panel">
    <div class="panel-toolbar">
        <input type="text" id="keyword" placeholder="금형번호 / 고객사 / 제품명 / 금형명" style="width:280px"
               onkeydown="if(event.key==='Enter') loadList()">
        <select id="gradeFilter" onchange="loadList()">
            <option value="">등급 전체</option>
            <option value="A">A 등급</option>
            <option value="B">B 등급</option>
            <option value="C">C 등급</option>
        </select>
        <button class="btn btn-primary btn-sm" onclick="loadList()">검색</button>
        <button class="btn btn-ghost btn-sm" onclick="resetFilter()">초기화</button>
    </div>

    <table class="list-table">
        <thead>
            <tr>
                <th style="width:40px">No</th>
                <th style="width:120px">금형번호</th>
                <th style="width:80px">고객사</th>
                <th style="width:70px">차종</th>
                <th>금형명</th>
                <th>제품명</th>
                <th style="width:85px">제작일</th>
                <th style="width:42px">등급</th>
                <th style="width:90px">점검타수</th>
                <th style="width:90px">교환타수</th>
            </tr>
        </thead>
        <tbody id="listBody">
            <tr class="empty-row"><td colspan="10">데이터를 불러오는 중...</td></tr>
        </tbody>
    </table>
</div>

<!-- 삭제 확인 모달 -->
<div class="modal-overlay" id="delModal">
    <div class="modal-box modal-confirm">
        <p id="delMsg"></p>
        <div class="modal-btn-row">
            <button class="modal-btn del" id="confirmDelBtn">삭제</button>
            <button class="modal-btn cancel" onclick="document.getElementById('delModal').classList.remove('active')">취소</button>
        </div>
    </div>
</div>

<script src="/mold/assets/js/main.js"></script>
<script>
async function loadList() {
    const keyword = document.getElementById('keyword').value;
    const grade   = document.getElementById('gradeFilter').value;
    const res  = await fetch(`/mold/api/molds.php?action=list&keyword=${encodeURIComponent(keyword)}&grade=${grade}`);
    const data = await res.json();

    const tbody = document.getElementById('listBody');
    if (!data.length) {
        tbody.innerHTML = '<tr class="empty-row"><td colspan="10">검색 결과가 없습니다.</td></tr>';
        return;
    }
    tbody.innerHTML = data.map((d, i) => `
        <tr onclick="location.href='/mold/card.php?id=${d.id}'" title="클릭하면 카드 열기">
            <td class="text-center">${i + 1}</td>
            <td class="text-center" style="font-weight:700;color:var(--primary)">${d.mold_no}</td>
            <td class="text-center">${d.customer || '-'}</td>
            <td class="text-center">${d.car_model || '-'}</td>
            <td>${d.mold_name || '-'}</td>
            <td>${d.product_name || '-'}</td>
            <td class="text-center">${d.made_date || '-'}</td>
            <td class="text-center grade-${d.mold_grade}">${d.mold_grade || '-'}</td>
            <td class="text-right">${parseInt(d.check_count || 0).toLocaleString()}</td>
            <td class="text-right">${parseInt(d.exchange_count || 0).toLocaleString()}</td>
        </tr>
    `).join('');
}

function resetFilter() {
    document.getElementById('keyword').value = '';
    document.getElementById('gradeFilter').value = '';
    loadList();
}

let delTargetId = null;
function confirmDelete(id, moldNo) {
    delTargetId = id;
    document.getElementById('delMsg').innerHTML =
        `<b>${moldNo}</b>을(를) 삭제하시겠습니까?<br><small style="color:var(--danger)">삭제 후 복구할 수 없습니다.</small>`;
    document.getElementById('delModal').classList.add('active');
}
document.getElementById('confirmDelBtn').onclick = async function () {
    if (!delTargetId) return;
    const res  = await fetch('/mold/api/molds.php?action=delete', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ id: delTargetId })
    });
    const data = await res.json();
    document.getElementById('delModal').classList.remove('active');
    if (data.success) { showToast('삭제 완료'); loadList(); }
    else showToast(data.error || '삭제 실패', 'error');
};

loadList();
</script>

<?php require_once 'includes/layout_end.php'; ?>
