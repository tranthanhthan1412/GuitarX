<main class="w-100 py-5 bg-light d-flex align-items-center justify-content-center" style="min-height: 70vh;">
    <div class="card border-0 shadow-sm rounded-3 p-5 text-center bg-white" style="max-width: 600px; width: 100%;">
        <span class="material-symbols-outlined text-success mb-3" style="font-size: 80px;">check_circle</span>
        <h2 class="font-display-sm fw-bold text-dark mb-3">Đặt hàng thành công!</h2>
        <p class="text-muted font-body-md mb-4">
            Cảm ơn bạn đã tin tưởng và mua sắm tại GuitarX. Mã đơn hàng của bạn là 
            <strong class="text-secondary-custom fs-5">#<?php echo $orderId; ?></strong>.
            <br><br>Chúng tôi sẽ sớm liên hệ với bạn theo thông tin đã cung cấp để xác nhận đơn hàng và tiến hành giao hàng trong thời gian sớm nhất.
        </p>
        <a href="<?= BASE_URL ?>index.php" class="btn btn-secondary-custom px-5 py-3 font-headline-sm rounded-2 shadow-sm">
            TIẾP TỤC MUA SẮM
        </a>
    </div>
</main>
