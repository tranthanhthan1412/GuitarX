<style>
/* Premium Footer Styling */
.footer-premium {
    background-color: #1a1a1a;
    color: #e0e0e0;
    border-top: 4px solid var(--color-secondary, #e63946);
    position: relative;
    overflow: hidden;
}
.footer-premium::before {
    content: '';
    position: absolute;
    top: 0; left: 0; width: 100%; height: 100%;
    background: radial-gradient(circle at 80% 20%, rgba(230, 57, 70, 0.05), transparent 40%);
    pointer-events: none;
}
.footer-title {
    color: #ffffff;
    font-weight: 700;
    letter-spacing: 1px;
    margin-bottom: 1.5rem;
    position: relative;
    padding-bottom: 10px;
}
.footer-title::after {
    content: '';
    position: absolute;
    left: 0; bottom: 0;
    width: 40px; height: 3px;
    background-color: var(--color-secondary, #e63946);
    border-radius: 2px;
}
.footer-link {
    color: #a0a0a0;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-block;
}
.footer-link:hover {
    color: var(--color-secondary, #e63946);
    transform: translateX(5px);
}
.social-btn-premium {
    width: 40px; height: 40px;
    border-radius: 50%;
    background-color: #2a2a2a;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    transition: all 0.3s ease;
}
.social-btn-premium:hover {
    background-color: var(--color-secondary, #e63946);
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(230, 57, 70, 0.3);
}
.footer-bottom {
    background-color: #111111;
    border-top: 1px solid #2a2a2a;
}
.footer-contact-item {
    transition: all 0.3s ease;
}
.footer-contact-item:hover {
    color: #ffffff;
}
</style>

<!-- Footer -->
<footer class="footer-premium py-5 mt-5">
    <div class="container-max-custom px-desktop-custom position-relative z-1">
        <div class="row g-5">
            <!-- Brand Column -->
            <div class="col-12 col-md-6 col-lg-4">
                <a class="font-headline-lg text-white fw-bold text-decoration-none d-block mb-3" href="#">GuitarX</a>
                <p class="font-body-md leading-relaxed mb-4" style="color: #a0a0a0;">Điểm đến hàng đầu Việt Nam về guitar và nhạc cụ chuyên nghiệp. Đồng hành cùng các nghệ sĩ từ năm 2005.</p>
                <div class="d-flex gap-2">
                    <button class="social-btn-premium"><span class="material-symbols-outlined fs-5">public</span></button>
                    <button class="social-btn-premium"><span class="material-symbols-outlined fs-5">alternate_email</span></button>
                    <button class="social-btn-premium"><span class="material-symbols-outlined fs-5">play_circle</span></button>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-12 col-md-6 col-lg-2">
                <h4 class="footer-title font-label-md text-uppercase">Thông tin</h4>
                <ul class="list-unstyled space-y-3 m-0 p-0">
                    <li class="mb-2"><a class="footer-link font-body-sm" href="#">Về chúng tôi</a></li>
                    <li class="mb-2"><a class="footer-link font-body-sm" href="#">Hệ thống cửa hàng</a></li>
                    <li class="mb-2"><a class="footer-link font-body-sm" href="#">Tin tức & Sự kiện</a></li>
                    <li class="mb-2"><a class="footer-link font-body-sm" href="#">Góc nghệ sĩ</a></li>
                </ul>
            </div>

            <!-- Customer Service -->
            <div class="col-12 col-md-6 col-lg-3">
                <h4 class="footer-title font-label-md text-uppercase">Hỗ trợ khách hàng</h4>
                <ul class="list-unstyled space-y-3 m-0 p-0">
                    <li class="mb-2"><a class="footer-link font-body-sm" href="#">Chính sách đổi trả</a></li>
                    <li class="mb-2"><a class="footer-link font-body-sm" href="#">Chính sách bảo hành</a></li>
                    <li class="mb-2"><a class="footer-link font-body-sm" href="#">Hướng dẫn mua hàng</a></li>
                    <li class="mb-2"><a class="footer-link font-body-sm" href="#">Tra cứu đơn hàng</a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div class="col-12 col-md-6 col-lg-3">
                <h4 class="footer-title font-label-md text-uppercase">Liên hệ</h4>
                <div class="space-y-3">
                    <div class="d-flex gap-3 footer-contact-item mb-3">
                        <span class="material-symbols-outlined text-secondary-custom">location_on</span>
                        <p class="font-body-sm mb-0">290 An Dương Vương, Phường 4, Quận 5, TP.HCM</p>
                    </div>
                    <div class="d-flex gap-3 footer-contact-item mb-3">
                        <span class="material-symbols-outlined text-secondary-custom">call</span>
                        <p class="font-body-sm mb-0">Hotline: 1900 6717</p>
                    </div>
                    <div class="d-flex gap-3 footer-contact-item mb-4">
                        <span class="material-symbols-outlined text-secondary-custom">mail</span>
                        <p class="font-body-sm mb-0">info@guitarx.vn</p>
                    </div>
                    <div>
                        <img alt="BCT Certificate" class="img-fluid opacity-75 hover-opacity-100 transition-all cursor-pointer" style="height: 45px;" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC0u_FXHHRs2AZp_6djDT51JpjLrvxucvFP9lGBBkSpZl9Cq6NkjFxa9Nww6uJIabTeVBZhPQ1bIMC5ET7HoP0lPyQY-t79uuxrYlgNUFJ9IDbTwqantc-8ilL7_BbLyJadBH_kLmDEXMvHc8kZDKkNnEQHIXNtySwWb-9x7Pw8WLnsqCoM7LfWRzmStwyLJqx4y8_yhlsxjONBhcsc8giDgoPpa3fhCbycWAp0fdCoYkF5PoR_wRCQeQoVGpcepjR99Ku_B1Ima5Q" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<div class="footer-bottom py-3">
    <div class="container-max-custom px-desktop-custom">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
            <p class="mb-2 mb-md-0 font-label-sm" style="color: #666;">© 2026 GuitarX. Nền tảng bán lẻ nhạc cụ số 1 Việt Nam.</p>
            <div class="d-flex gap-4">
                <a class="footer-link font-label-sm" style="color: #666;" href="#">Điều khoản sử dụng</a>
                <a class="footer-link font-label-sm" style="color: #666;" href="#">Chính sách bảo mật</a>
            </div>
        </div>
    </div>
</div>

<!-- Chat FAB & Box -->
<style>
    .btn-chat-fab {
        width: 60px;
        height: 60px;
        background-color: #e63946;
        border: none;
        transition: all 0.3s ease;
    }
    .btn-chat-fab:hover {
        background-color: #c9222f;
        transform: scale(1.05);
    }
    .fab-tooltip {
        right: 100%;
        margin-right: 10px;
        top: 50%;
        transform: translateY(-50%);
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        pointer-events: none;
        border: 1px solid rgba(0,0,0,0.05);
    }
    .btn-chat-fab:hover .fab-tooltip {
        opacity: 1;
        visibility: visible;
        margin-right: 18px;
    }
    #chatBox {
        position: fixed;
        bottom: 90px;
        right: 24px;
        width: 350px;
        height: 450px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 5px 25px rgba(0,0,0,0.15);
        display: none;
        flex-direction: column;
        z-index: 1000;
        overflow: hidden;
    }
    .chat-header {
        background: #e63946;
        color: #fff;
        padding: 15px;
        font-weight: bold;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .chat-body {
        flex: 1;
        padding: 15px;
        overflow-y: auto;
        background: #f8f9fa;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .chat-msg {
        max-width: 80%;
        padding: 8px 12px;
        border-radius: 15px;
        font-size: 0.9rem;
        word-wrap: break-word;
    }
    .chat-msg.admin {
        align-self: flex-start;
        background: #e9ecef;
        color: #000;
        border-bottom-left-radius: 2px;
    }
    .chat-msg.user {
        align-self: flex-end;
        background: #e63946;
        color: #fff;
        border-bottom-right-radius: 2px;
    }
    .chat-footer {
        padding: 10px;
        background: #fff;
        border-top: 1px solid #eee;
        display: flex;
        gap: 5px;
    }
    .chat-footer input {
        flex: 1;
        border: 1px solid #ddd;
        border-radius: 20px;
        padding: 8px 15px;
        outline: none;
    }
    .chat-footer button {
        background: #e63946;
        color: #fff;
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>

<div id="chatBox">
    <div class="chat-header">
        <span>Tư vấn trực tuyến</span>
        <span class="material-symbols-outlined" style="cursor:pointer;" onclick="toggleChat()">close</span>
    </div>
    <div class="chat-body" id="chatBody">
        <?php if (!isset($_SESSION['user_id'])): ?>
            <div class="text-center text-muted mt-5">Vui lòng đăng nhập để chat.</div>
        <?php endif; ?>
    </div>
    <?php if (isset($_SESSION['user_id'])): ?>
    <form class="chat-footer" id="chatForm" onsubmit="sendChat(event)">
        <input type="text" id="chatInput" placeholder="Nhập tin nhắn..." autocomplete="off" required>
        <button type="submit"><span class="material-symbols-outlined">send</span></button>
    </form>
    <?php endif; ?>
</div>

<div class="position-fixed bottom-0 end-0 m-4 z-3">
    <button onclick="toggleChat()" class="btn btn-chat-fab rounded-circle shadow-lg d-flex align-items-center justify-content-center position-relative group-fab">
        <span class="material-symbols-outlined fs-3 text-white">forum</span>
        <?php 
        $unreadCount = 0;
        if (isset($_SESSION['user_id']) && isset($db)) {
            require_once __DIR__ . '/../model/m_chat.php';
            $cModel = new ChatModel($db);
            $unreadCount = $cModel->countUnreadForUser($_SESSION['user_id']);
        }
        ?>
        <?php if ($unreadCount > 0): ?>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-circle bg-danger border border-white d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 10px; font-weight: bold;"><?= $unreadCount ?></span>
        <?php endif; ?>
        <div class="fab-tooltip position-absolute bg-white text-dark px-3 py-2 rounded-3 shadow font-bold text-nowrap" style="font-size: 14px; font-weight: 600;">
            Chat với tư vấn viên
        </div>
    </button>
</div>

<script>
let chatOpen = false;
let lastMessageCount = 0;
let chatInterval = null;

function toggleChat() {
    chatOpen = !chatOpen;
    document.getElementById('chatBox').style.display = chatOpen ? 'flex' : 'none';
    if (chatOpen) {
        fetchMessages();
        chatInterval = setInterval(fetchMessages, 3000);
    } else {
        clearInterval(chatInterval);
    }
}

function fetchMessages() {
    fetch('<?= BASE_URL ?>index.php?act=chat_api_get')
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            if (data.data.length !== lastMessageCount) {
                lastMessageCount = data.data.length;
                let html = '';
                data.data.forEach(msg => {
                    let className = msg.Is_Admin_Reply == 1 ? 'admin' : 'user';
                    html += `<div class="chat-msg ${className}">${msg.NoiDung}</div>`;
                });
                if (html === '') html = '<div class="text-center text-muted mt-3" style="font-size:0.8rem;">Bắt đầu trò chuyện...</div>';
                const body = document.getElementById('chatBody');
                body.innerHTML = html;
                body.scrollTop = body.scrollHeight; // auto scroll to bottom
            }
        }
    });
}

function sendChat(e) {
    e.preventDefault();
    const input = document.getElementById('chatInput');
    const text = input.value.trim();
    if (!text) return;
    
    const body = document.getElementById('chatBody');
    body.innerHTML += `<div class="chat-msg user">${text}</div>`;
    body.scrollTop = body.scrollHeight;
    lastMessageCount++;
    input.value = '';

    let formData = new FormData();
    formData.append('message', text);

    fetch('<?= BASE_URL ?>index.php?act=chat_api_send', {
        method: 'POST',
        body: formData
    }).then(res => res.json()).then(data => {
        if(data.status !== 'success') {
            console.error('Lỗi gửi tin nhắn');
        }
    });
}
</script>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Simple countdown logic for Flash Sale
function updateTimer() {
    const h = document.getElementById('hours');
    const m = document.getElementById('minutes');
    const s = document.getElementById('seconds');

    if (!h || !m || !s) return;

    let hours = parseInt(h.innerText);
    let mins = parseInt(m.innerText);
    let secs = parseInt(s.innerText);

    setInterval(() => {
        secs--;
        if (secs < 0) {
            secs = 59;
            mins--;
        }
        if (mins < 0) {
            mins = 59;
            hours--;
        }
        if (hours < 0) {
            hours = 2;
            mins = 45;
            secs = 12; // Reset for demo
        }

        h.innerText = hours.toString().padStart(2, '0');
        m.innerText = mins.toString().padStart(2, '0');
        s.innerText = secs.toString().padStart(2, '0');
    }, 1000);
}
updateTimer();
</script>
</body>

</html>