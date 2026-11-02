// JavaScript thuần, chỉ hỗ trợ trải nghiệm (backend vẫn kiểm tra đầy đủ).
document.addEventListener('submit', function (event) {
  var form = event.target;

  // 1) Xác nhận trước khi xóa: <form data-confirm="...">
  var message = form.getAttribute('data-confirm');
  if (message && !window.confirm(message)) {
    event.preventDefault();
    return;
  }

  // 2) Chống bấm lưu liên tục: chỉ khi form đã hợp lệ và bắt đầu gửi.
  if (event.defaultPrevented || (form.checkValidity && !form.checkValidity())) {
    return;
  }
  var buttons = form.querySelectorAll('button[type="submit"]');
  setTimeout(function () {
    buttons.forEach(function (button) { button.disabled = true; });
  }, 0);
});

// Quay lại bằng nút Back của trình duyệt: bật lại các nút đã bị khóa.
window.addEventListener('pageshow', function (event) {
  if (event.persisted) {
    document.querySelectorAll('button[type="submit"]:disabled').forEach(function (button) {
      button.disabled = false;
    });
  }
});
