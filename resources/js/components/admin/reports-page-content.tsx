import { useForm } from '@inertiajs/react';
import { FormEvent } from 'react';
import { PageHeader } from '@/components/page-header';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

export function ReportsPageContent({ runs }: { runs: { data: Array<{ id: number; date_from: string; date_to: string; transaction_count: number; rule_count: number }> } }) {
    const form = useForm({ date_from: new Date().toISOString().slice(0, 8) + '01', date_to: new Date().toISOString().slice(0, 10), minimum_support: 0.1, minimum_confidence: 0.6, minimum_lift: 0 });
    const submit = (event: FormEvent) => { event.preventDefault(); form.post('/admin/apriori'); };
    return <div className="space-y-6 p-4 md:p-6"><PageHeader title="Laporan & Apriori" description="Jalankan analisis berdasarkan order completed pada periode pickup."/><Card><CardHeader><CardTitle>Jalankan analisis</CardTitle></CardHeader><CardContent><form onSubmit={submit} className="grid gap-3 sm:grid-cols-5"><input type="date" value={form.data.date_from} onChange={(e) => form.setData('date_from', e.target.value)}/><input type="date" value={form.data.date_to} onChange={(e) => form.setData('date_to', e.target.value)}/><input type="number" step="0.01" min="0" max="1" value={form.data.minimum_support} onChange={(e) => form.setData('minimum_support', Number(e.target.value))}/><input type="number" step="0.01" min="0" max="1" value={form.data.minimum_confidence} onChange={(e) => form.setData('minimum_confidence', Number(e.target.value))}/><Button disabled={form.processing}>Jalankan</Button></form></CardContent></Card><Card><CardHeader><CardTitle>Riwayat analisis</CardTitle></CardHeader><CardContent className="space-y-3">{runs.data.map((run) => <div className="flex justify-between rounded border p-3" key={run.id}><span>{run.date_from} s/d {run.date_to}</span><span>{run.transaction_count} transaksi · {run.rule_count} rule</span></div>)}{runs.data.length === 0 && <p className="text-muted-foreground">Belum ada analisis.</p>}</CardContent></Card></div>;
}
