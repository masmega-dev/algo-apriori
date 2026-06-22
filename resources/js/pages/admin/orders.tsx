import { Head } from '@inertiajs/react';
import { Download, Plus } from 'lucide-react';
import { AdminOrderTable } from '@/components/admin-order-table';
import { PageHeader } from '@/components/page-header';
import { Button } from '@/components/ui/button';

export default function Orders() { return <><Head title="Daftar Order"/><div className="space-y-6 p-4 md:p-6"><PageHeader title="Daftar Order" description="Kelola pesanan pelanggan dan jadwal pickup toko." actions={<><Button variant="outline"><Download className="size-4"/>Export</Button><Button><Plus className="size-4"/>Order manual</Button></>}/><AdminOrderTable/></div></>; }
