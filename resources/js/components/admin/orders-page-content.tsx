import { Link, router } from '@inertiajs/react';
import { Search, Trash2 } from 'lucide-react';
import { FormEvent, useState } from 'react';
import { PageHeader } from '@/components/page-header';
import { StatusBadge } from '@/components/status-badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { formatRupiah } from '@/lib/formatters';
import type { OrderStatus } from '@/types/cake-shop';

type Order = { id: number; order_number: string; customer_name_snapshot: string; pickup_at: string; grand_total: string; status: OrderStatus };
type Props = { orders: { data: Order[]; links: Array<{ url: string | null; label: string; active: boolean }> }; filters: { search?: string; status?: string } };

export function OrdersPageContent({ orders, filters }: Props) {
    const [search, setSearch] = useState(filters.search ?? '');
    const submit = (event: FormEvent) => { event.preventDefault(); router.get('/admin/orders', { search, status: filters.status }, { preserveState: true, replace: true }); };
    const filter = (status: string) => router.get('/admin/orders', { search, status: status === 'all' ? undefined : status }, { preserveState: true, replace: true });
    return <div className="space-y-6 p-4 md:p-6"><PageHeader title="Daftar Order" description="Kelola pesanan pelanggan dan jadwal pickup toko."/><form onSubmit={submit} className="flex flex-col gap-3 sm:flex-row"><div className="relative flex-1"><Search className="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground"/><Input value={search} onChange={(event) => setSearch(event.target.value)} className="pl-9" placeholder="Cari nomor order atau pelanggan"/></div><Select value={filters.status ?? 'all'} onValueChange={filter}><SelectTrigger className="sm:w-44"><SelectValue/></SelectTrigger><SelectContent><SelectItem value="all">Semua status</SelectItem><SelectItem value="pending">Menunggu</SelectItem><SelectItem value="completed">Selesai</SelectItem><SelectItem value="cancelled">Dibatalkan</SelectItem></SelectContent></Select><Button type="submit">Cari</Button></form><div className="overflow-x-auto rounded-xl border"><table className="w-full min-w-[720px] text-sm"><thead className="bg-muted/50 text-left text-muted-foreground"><tr><th className="p-4">Order</th><th className="p-4">Pickup</th><th className="p-4">Total</th><th className="p-4">Status</th><th className="p-4 text-right">Aksi</th></tr></thead><tbody>{orders.data.map((order) => <tr key={order.id} className="border-t"><td className="p-4"><Link className="font-medium hover:text-primary" href={`/admin/orders/${order.id}`}>{order.order_number}</Link><p className="text-xs text-muted-foreground">{order.customer_name_snapshot}</p></td><td className="p-4">{new Date(order.pickup_at).toLocaleString('id-ID')}</td><td className="p-4 font-medium">{formatRupiah(Number(order.grand_total))}</td><td className="p-4"><StatusBadge status={order.status}/></td><td className="p-4 text-right"><div className="flex justify-end gap-2">{order.status === 'pending' && <Button size="sm" onClick={() => router.patch(`/admin/orders/${order.id}/complete`)}>Selesaikan</Button>}<Button size="icon" variant="ghost" onClick={() => confirm('Hapus order ini?') && router.delete(`/admin/orders/${order.id}`)}><Trash2 className="size-4"/></Button></div></td></tr>)}{orders.data.length === 0 && <tr><td colSpan={5} className="p-10 text-center text-muted-foreground">Order tidak ditemukan.</td></tr>}</tbody></table></div><div className="flex justify-end gap-1">{orders.links.map((link, index) => <Button key={index} size="sm" variant={link.active ? 'default' : 'outline'} disabled={!link.url} onClick={() => link.url && router.visit(link.url)} dangerouslySetInnerHTML={{ __html: link.label }}/>)}</div></div>;
}
