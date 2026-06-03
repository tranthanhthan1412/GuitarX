<!-- ===== FOOTER ===== -->
<footer class="bg-primary-custom text-white pt-5 pb-3 mt-0">
    <div class="container-max-custom px-desktop-custom">
        <div class="row g-5 pb-4">
            <!-- Brand Column -->
            <div class="col-12 col-md-4">
                <a class="font-display-lg fw-bold text-white text-decoration-none d-inline-block mb-3" href="index.php">VIET MUSIC</a>
                <p class="font-body-md text-white opacity-75 mb-4">
                    Vietnam's premier destination for professional guitars and musical instruments. Serving musicians since 2005.
                </p>
                <div class="d-flex gap-3">
                    <a href="#" class="btn btn-sm bg-white bg-opacity-10 text-white border-0 rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                        <span class="material-symbols-outlined" style="font-size:18px;">public</span>
                    </a>
                    <a href="#" class="btn btn-sm bg-white bg-opacity-10 text-white border-0 rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                        <span class="material-symbols-outlined" style="font-size:18px;">mail</span>
                    </a>
                    <a href="#" class="btn btn-sm bg-white bg-opacity-10 text-white border-0 rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                        <span class="material-symbols-outlined" style="font-size:18px;">phone</span>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-6 col-md-2">
                <h4 class="font-label-md text-uppercase text-white opacity-50 tracking-wider mb-3">Shop</h4>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none opacity-75 font-body-md footer-link">Acoustic Guitars</a></li>
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none opacity-75 font-body-md footer-link">Electric Guitars</a></li>
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none opacity-75 font-body-md footer-link">Classical Guitars</a></li>
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none opacity-75 font-body-md footer-link">Bass Guitars</a></li>
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none opacity-75 font-body-md footer-link">Accessories</a></li>
                </ul>
            </div>

            <!-- Support -->
            <div class="col-6 col-md-2">
                <h4 class="font-label-md text-uppercase text-white opacity-50 tracking-wider mb-3">Support</h4>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none opacity-75 font-body-md footer-link">Contact Us</a></li>
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none opacity-75 font-body-md footer-link">Shipping Policy</a></li>
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none opacity-75 font-body-md footer-link">Returns</a></li>
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none opacity-75 font-body-md footer-link">Warranty</a></li>
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none opacity-75 font-body-md footer-link">FAQ</a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div class="col-12 col-md-4">
                <h4 class="font-label-md text-uppercase text-white opacity-50 tracking-wider mb-3">Visit Us</h4>
                <div class="d-flex gap-2 mb-2 align-items-start">
                    <span class="material-symbols-outlined opacity-75" style="font-size:18px;margin-top:2px;">location_on</span>
                    <p class="text-white opacity-75 font-body-md mb-0">123 Nguyen Hue Street, District 1,<br>Ho Chi Minh City, Vietnam</p>
                </div>
                <div class="d-flex gap-2 mb-2 align-items-center">
                    <span class="material-symbols-outlined opacity-75" style="font-size:18px;">call</span>
                    <a href="tel:+84901234567" class="text-white opacity-75 text-decoration-none font-body-md footer-link">+84 90 123 4567</a>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <span class="material-symbols-outlined opacity-75" style="font-size:18px;">schedule</span>
                    <span class="text-white opacity-75 font-body-md">Mon–Sat: 9:00 – 20:00</span>
                </div>
            </div>
        </div>

        <!-- Divider -->
        <div class="border-top border-white border-opacity-10 pt-3 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
            <p class="text-white opacity-50 font-label-sm mb-0">© <?php echo date('Y'); ?> VIET MUSIC. All rights reserved.</p>
            <div class="d-flex gap-3">
                <a href="#" class="text-white opacity-50 text-decoration-none font-label-sm footer-link">Privacy Policy</a>
                <a href="#" class="text-white opacity-50 text-decoration-none font-label-sm footer-link">Terms of Use</a>
            </div>
        </div>
    </div>
</footer>

<style>
.footer-link { transition: opacity 0.2s ease; }
.footer-link:hover { opacity: 1 !important; }
</style>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Flash-sale countdown timer -->
<script>
(function () {
    // Set a 3-hour countdown from page load
    const endTime = Date.now() + 3 * 60 * 60 * 1000;

    function pad(n) { return String(n).padStart(2, '0'); }

    function tick() {
        const diff = Math.max(0, endTime - Date.now());
        const h = Math.floor(diff / 3600000);
        const m = Math.floor((diff % 3600000) / 60000);
        const s = Math.floor((diff % 60000) / 1000);

        const elH = document.getElementById('hours');
        const elM = document.getElementById('minutes');
        const elS = document.getElementById('seconds');

        if (elH) elH.textContent = pad(h);
        if (elM) elM.textContent = pad(m);
        if (elS) elS.textContent = pad(s);

        if (diff > 0) setTimeout(tick, 1000);
    }

    tick();
})();
</script>

</body>
</html>
