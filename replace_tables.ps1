$files = Get-ChildItem -Path "c:\xampp\htdocs\GuitarX" -Recurse -Filter "*.php"
$replacements = [ordered]@{
    '`XepHang`' = '`xephang`';
    '`NguoiDung`' = '`nguoidung`';
    '`DanhMuc`' = '`danhmuc`';
    '`SanPham`' = '`sanpham`';
    '`DanhGia`' = '`danhgia`';
    '`GioHang`' = '`giohang`';
    '`ChiTietGioHang`' = '`chitietgiohang`';
    '`MaGiamGia`' = '`magiamgia`';
    '`PhuongThucThanhToan`' = '`phuongthucthanhtoan`';
    '`DonHang`' = '`donhang`';
    '`ChiTietDonHang`' = '`chitietdonhang`';
    '`DiaChiGiaoHang`' = '`diachigiaohang`';
    '`GhiChuGiaoHang`' = '`ghichugiaohang`';
    '`YeuThich`' = '`yeuthich`'
}

foreach ($file in $files) {
    $content = Get-Content $file.FullName -Raw
    $changed = $false
    foreach ($key in $replacements.Keys) {
        if ($content -match [regex]::Escape($key)) {
            $content = $content.Replace($key, $replacements[$key])
            $changed = $true
        }
    }
    if ($changed) {
        [IO.File]::WriteAllText($file.FullName, $content, [System.Text.Encoding]::UTF8)
        Write-Output "Updated $($file.FullName)"
    }
}
