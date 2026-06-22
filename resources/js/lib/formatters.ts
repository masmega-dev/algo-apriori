export const formatRupiah = (value: number): string =>
    new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(value);

export const formatOrderStatus = (status: string): string =>
    ({ pending: 'Menunggu', completed: 'Selesai', cancelled: 'Dibatalkan' })[
        status
    ] ?? status;
