<!-- Footer -->
<footer class="bg-surface-container-highest border-t py-5 mt-5">
    <div class="container-max-custom px-desktop-custom">
        <div class="row g-4 row-cols-1 row-cols-md-2 row-cols-lg-4">
            <!-- Brand Column -->
            <div class="space-y-4">
                <a class="font-headline-md text-primary-custom fw-bold text-decoration-none d-block mb-3"
                    href="#">GuitarX</a>
                <p class="text-muted font-body-md leading-relaxed">Điểm đến hàng đầu Việt Nam về guitar và nhạc cụ
                    chuyên nghiệp. Đồng hành cùng các nghệ sĩ từ năm 2005.</p>
                <div class="d-flex gap-2">
                    <button
                        class="btn social-btn rounded-circle bg-white text-primary-custom d-flex align-items-center justify-content-center"><span
                            class="material-symbols-outlined">public</span></button>
                    <button
                        class="btn social-btn rounded-circle bg-white text-primary-custom d-flex align-items-center justify-content-center"><span
                            class="material-symbols-outlined">alternate_email</span></button>
                    <button
                        class="btn social-btn rounded-circle bg-white text-primary-custom d-flex align-items-center justify-content-center"><span
                            class="material-symbols-outlined">play_circle</span></button>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h4 class="font-label-md text-uppercase mb-4 tracking-wider fw-bold">Thông tin</h4>
                <ul class="list-unstyled space-y-2">
                    <li class="mb-2"><a class="text-muted text-decoration-none hover-secondary-custom" href="#">Về chúng
                            tôi</a></li>
                    <li class="mb-2"><a class="text-muted text-decoration-none hover-secondary-custom" href="#">Địa
                            chỉ</a></li>
                    <li class="mb-2"><a class="text-muted text-decoration-none hover-secondary-custom" href="#">Tin
                            tức</a>
                    </li>
                    <li class="mb-2"><a class="text-muted text-decoration-none hover-secondary-custom" href="#"></a>
                    </li>
                </ul>
            </div>

            <!-- Customer Service -->
            <div>
                <h4 class="font-label-md text-uppercase mb-4 tracking-wider fw-bold">Hỗ trợ khách hàng</h4>
                <ul class="list-unstyled space-y-2">
                    <li class="mb-2"><a class="text-muted text-decoration-none hover-secondary-custom" href="#">Giao
                            hàng và hoàn trả</a></li>
                    <li class="mb-2"><a class="text-muted text-decoration-none hover-secondary-custom" href="#">Chính
                            sách bảo hành</a></li>
                    <li class="mb-2"><a class="text-muted text-decoration-none hover-secondary-custom" href="#">Dịch vụ
                            bảo trì</a></li>
                    <li class="mb-2"><a class="text-muted text-decoration-none hover-secondary-custom" href="#">Liên hệ
                            trung tâm dịch vụ</a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div>
                <h4 class="font-label-md text-uppercase mb-4 tracking-wider fw-bold">Liên hệ</h4>
                <div class="space-y-3">
                    <div class="d-flex gap-3 mb-3">
                        <span class="material-symbols-outlined text-primary-custom">location_on</span>
                        <p class="text-muted font-body-md mb-0">290 An Duong Vuong, Ward 4, District 5, HCMC, Vietnam
                        </p>
                    </div>
                    <div class="d-flex gap-3 mb-3">
                        <span class="material-symbols-outlined text-primary-custom">call</span>
                        <p class="text-muted font-body-md mb-0">1900 6717</p>
                    </div>
                    <div class="d-flex gap-3 mb-4">
                        <span class="material-symbols-outlined text-primary-custom">mail</span>
                        <p class="text-muted font-body-md mb-0">info@vietmusic.vn</p>
                    </div>
                    <div>
                        <img alt="BCT Certificate" class="img-fluid opacity-70 grayscale-img cursor-pointer"
                            style="height: 40px;"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuC0u_FXHHRs2AZp_6djDT51JpjLrvxucvFP9lGBBkSpZl9Cq6NkjFxa9Nww6uJIabTeVBZhPQ1bIMC5ET7HoP0lPyQY-t79uuxrYlgNUFJ9IDbTwqantc-8ilL7_BbLyJadBH_kLmDEXMvHc8kZDKkNnEQHIXNtySwWb-9x7Pw8WLnsqCoM7LfWRzmStwyLJqx4y8_yhlsxjONBhcsc8giDgoPpa3fhCbycWAp0fdCoYkF5PoR_wRCQeQoVGpcepjR99Ku_B1Ima5Q" />
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-4 text-muted">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center text-muted font-label-sm">
            <p class="mb-2 mb-md-0">© 2026 GuitarX. All Rights Reserved.</p>
            <div class="d-flex gap-4">
                <a class="text-muted text-decoration-none hover-secondary-custom" href="#">Privacy Policy</a>
                <a class="text-muted text-decoration-none hover-secondary-custom" href="#">Terms of Service</a>
                <a class="text-muted text-decoration-none hover-secondary-custom" href="#">Cookie Settings</a>
            </div>
        </div>
    </div>
</footer>

<!-- Chat FAB -->
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
</style>
<div class="position-fixed bottom-0 end-0 m-4 z-3">
    <button class="btn btn-chat-fab rounded-circle shadow-lg d-flex align-items-center justify-content-center position-relative group-fab">
        <span class="material-symbols-outlined fs-3 text-white">forum</span>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-circle bg-danger border border-white d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 10px; font-weight: bold;">1</span>
        <div class="fab-tooltip position-absolute bg-white text-dark px-3 py-2 rounded-3 shadow font-bold text-nowrap" style="font-size: 14px; font-weight: 600;">
            Chat với tư vấn viên
        </div>
    </button>
</div>

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