import { BarChart3, Clock3, History, Info } from 'lucide-react';
import type { ReactNode } from 'react';
import { Pie, PieChart } from 'recharts';
import { AnalysisParameterCard } from '@/components/admin/reports/analysis-parameter-card';
import { ruleItems } from '@/components/admin/reports/report-types';
import type {
    AnalysisRun,
    Itemset,
    Overview,
    Pagination,
    Rule,
} from '@/components/admin/reports/report-types';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    ChartContainer,
    ChartLegend,
    ChartTooltip,
    ChartTooltipContent,
} from '@/components/ui/chart';
import type { ChartConfig } from '@/components/ui/chart';

type Defaults = {
    minimum_support: number;
    minimum_confidence: number;
    minimum_lift: number;
    maximum_itemset: number;
    maximum_rules: number;
    minimum_occurrence: number;
};

type Props = {
    run: AnalysisRun | null;
    itemsets: Pagination<Itemset> | null;
    rules: Pagination<Rule> | null;
    history: Pagination<AnalysisRun>;
    overview: Overview | null;
    filters: Record<string, string | undefined>;
    defaults: Defaults;
};

type AprioriChartDatum = {
    metric: 'support' | 'confidence' | 'lift';
    label: string;
    value: number;
    fill: string;
};

const aprioriChartConfig = {
    value: {
        label: 'Persentase',
    },
    support: {
        label: 'Support',
        color: 'var(--chart-1)',
    },
    confidence: {
        label: 'Confidence',
        color: 'var(--chart-2)',
    },
    lift: {
        label: 'Lift',
        color: 'var(--chart-3)',
    },
} satisfies ChartConfig;

export function ReportsPageContent({ run, rules, overview, defaults }: Props) {
    const aprioriRules = overview?.rules ?? rules?.data ?? [];

    return (
        <div className="space-y-6">
            <AnalysisParameterCard defaults={defaults} />

            {run === null ? (
                <ReportEmptyState />
            ) : (
                <>
                    <AnalysisStatusNotice run={run} />
                    <AnalysisSummaryCards run={run} />
                    <AprioriPairDonutCharts rules={aprioriRules} />
                </>
            )}
        </div>
    );
}

function AnalysisStatusNotice({ run }: { run: AnalysisRun }) {
    if (run.status === 'completed') {
        return null;
    }

    const message =
        run.status === 'failed'
            ? (run.error_message ?? 'Analisis gagal diselesaikan.')
            : 'Analisis sedang diproses. Hasil lama tidak dihapus sampai proses selesai.';

    return (
        <Card className="border-amber-200 bg-amber-50 text-amber-950 dark:border-amber-900/50 dark:bg-amber-950/30 dark:text-amber-100">
            <CardContent className="flex items-start gap-3 p-4 text-sm">
                <Info className="mt-0.5 size-4" />
                <span>{message}</span>
            </CardContent>
        </Card>
    );
}

function AnalysisSummaryCards({ run }: { run: AnalysisRun }) {
    return (
        <section className="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
            <SummaryCard
                label="Total Transaksi Dianalisis"
                value={run.transaction_count.toLocaleString('id-ID')}
                icon={<BarChart3 className="size-4" />}
            />
            <SummaryCard
                label="Produk Unik"
                value={run.unique_item_count.toLocaleString('id-ID')}
                icon={<Info className="size-4" />}
            />
            <SummaryCard
                label="Frequent Itemsets"
                value={run.frequent_itemset_count.toLocaleString('id-ID')}
                icon={<BarChart3 className="size-4" />}
            />
            <SummaryCard
                label="Aturan Asosiasi"
                value={run.rule_count.toLocaleString('id-ID')}
                icon={<History className="size-4" />}
            />
            <SummaryCard
                label="Durasi Analisis"
                value={formatDuration(run.execution_time_ms)}
                icon={<Clock3 className="size-4" />}
            />
        </section>
    );
}

function SummaryCard({
    label,
    value,
    icon,
}: {
    label: string;
    value: string;
    icon: ReactNode;
}) {
    return (
        <Card>
            <CardContent className="p-5">
                <div className="flex items-center justify-between text-muted-foreground">
                    <span className="text-xs font-medium tracking-wide uppercase">
                        {label}
                    </span>
                    {icon}
                </div>
                <div className="mt-3 text-2xl font-semibold">{value}</div>
            </CardContent>
        </Card>
    );
}

function AprioriPairDonutCharts({ rules }: { rules: Rule[] }) {
    const pairs = [
        { title: 'Lilin → Pisau', rule: findRulePair(rules, 'Lilin', 'Pisau') },
        { title: 'Pisau → Lilin', rule: findRulePair(rules, 'Pisau', 'Lilin') },
    ];

    return (
        <Card>
            <CardHeader>
                <CardTitle>Apriori Lilin dan Pisau</CardTitle>
                <CardDescription>
                    Ditampilkan sederhana: support, confidence, dan lift untuk
                    dua arah asosiasi.
                </CardDescription>
            </CardHeader>
            <CardContent>
                <div className="grid gap-4 md:grid-cols-2">
                    {pairs.map((pair) => (
                        <AprioriDonutCard
                            key={pair.title}
                            rule={pair.rule}
                            title={pair.title}
                        />
                    ))}
                </div>
            </CardContent>
        </Card>
    );
}

function AprioriDonutCard({
    title,
    rule,
}: {
    title: string;
    rule: Rule | null;
}) {
    if (rule === null) {
        return (
            <div className="rounded-2xl border bg-muted/20 p-5">
                <h3 className="font-semibold">{title}</h3>
                <p className="mt-3 text-sm leading-6 text-muted-foreground">
                    Belum ada aturan yang memenuhi parameter.
                </p>
            </div>
        );
    }

    const chartData = buildAprioriChartData(rule);

    return (
        <Card className="border bg-background shadow-none">
            <CardHeader className="items-center pb-0 text-center">
                <div className="flex w-full items-center justify-between gap-3">
                    <div className="text-left">
                        <CardTitle className="text-base">{title}</CardTitle>
                        <CardDescription>
                            {rule.support_count.toLocaleString('id-ID')}{' '}
                            transaksi
                        </CardDescription>
                    </div>
                    <Badge variant="outline">Lilin & Pisau</Badge>
                </div>
            </CardHeader>
            <CardContent className="pb-4">
                <ChartContainer
                    config={aprioriChartConfig}
                    className="mx-auto aspect-square h-[380px] max-h-[380px]"
                >
                    <PieChart>
                        <ChartTooltip
                            content={
                                <ChartTooltipContent
                                    hideLabel
                                    formatter={(value, name) => (
                                        <>
                                            <span className="text-muted-foreground">
                                                {
                                                    aprioriChartConfig[
                                                        String(
                                                            name,
                                                        ) as keyof typeof aprioriChartConfig
                                                    ]?.label
                                                }
                                            </span>
                                            <span className="font-mono font-medium text-foreground">
                                                {formatChartPercent(
                                                    Number(value),
                                                )}
                                            </span>
                                        </>
                                    )}
                                />
                            }
                        />
                        <Pie
                            data={chartData}
                            dataKey="value"
                            innerRadius={76}
                            label={renderPiePercentLabel}
                            labelLine={false}
                            nameKey="metric"
                            outerRadius={120}
                            paddingAngle={3}
                        />
                        <ChartLegend
                            content={<AprioriChartLegend />}
                            className="-translate-y-2 flex-wrap gap-2 *:basis-1/4 *:justify-center"
                        />
                    </PieChart>
                </ChartContainer>
            </CardContent>
        </Card>
    );
}

function AprioriChartLegend({ payload }: { payload?: unknown[] }) {
    if (!payload?.length) {
        return null;
    }

    return (
        <div className="flex flex-wrap items-center justify-center gap-2 pt-3">
            {payload.map((item) => {
                const legend = resolveLegendItem(item);

                if (legend === null) {
                    return null;
                }

                return (
                    <div
                        key={legend.metric}
                        className="flex items-center justify-center gap-2 text-xs"
                    >
                        <span
                            className="size-2.5 shrink-0 rounded-full"
                            style={{ backgroundColor: legend.color }}
                        />
                        <span className="font-medium text-muted-foreground">
                            {legend.label}
                        </span>
                        <span className="font-semibold text-foreground tabular-nums">
                            {formatChartPercent(legend.value)}
                        </span>
                    </div>
                );
            })}
        </div>
    );
}

function resolveLegendItem(item: unknown): {
    metric: string;
    label: string;
    value: number;
    color: string;
} | null {
    if (typeof item !== 'object' || item === null) {
        return null;
    }

    const legendItem = item as {
        color?: unknown;
        payload?: unknown;
        value?: unknown;
    };
    const payload = legendItem.payload;

    if (typeof payload !== 'object' || payload === null) {
        return null;
    }

    const datum = payload as Partial<AprioriChartDatum>;

    if (
        typeof datum.metric !== 'string' ||
        typeof datum.label !== 'string' ||
        typeof datum.value !== 'number'
    ) {
        return null;
    }

    return {
        metric: datum.metric,
        label: datum.label,
        value: datum.value,
        color:
            typeof legendItem.color === 'string'
                ? legendItem.color
                : (datum.fill ?? 'currentColor'),
    };
}

function buildAprioriChartData(rule: Rule): AprioriChartDatum[] {
    return [
        {
            metric: 'support',
            label: 'Support',
            value: toPercent(rule.support),
            fill: 'var(--color-support)',
        },
        {
            metric: 'confidence',
            label: 'Confidence',
            value: toPercent(rule.confidence),
            fill: 'var(--color-confidence)',
        },
        {
            metric: 'lift',
            label: 'Lift',
            value: toPercent(normalizeLift(rule.lift)),
            fill: 'var(--color-lift)',
        },
    ];
}

function toPercent(value: string | number): number {
    return clampRatio(Number(value)) * 100;
}

function formatChartPercent(value: number): string {
    return `${value.toLocaleString('id-ID', {
        maximumFractionDigits: 2,
    })}%`;
}

function renderPiePercentLabel(payload: unknown): ReactNode {
    if (!isPieLabelPayload(payload)) {
        return null;
    }

    const radius =
        payload.innerRadius +
        (payload.outerRadius - payload.innerRadius) * 0.55;
    const x =
        payload.cx + radius * Math.cos((-payload.midAngle * Math.PI) / 180);
    const y =
        payload.cy + radius * Math.sin((-payload.midAngle * Math.PI) / 180);

    return (
        <text
            x={x}
            y={y}
            fill="#ffffff"
            textAnchor="middle"
            dominantBaseline="central"
            className="text-[11px] font-semibold drop-shadow-sm"
        >
            {formatChartPercent(payload.value)}
        </text>
    );
}

function isPieLabelPayload(payload: unknown): payload is {
    cx: number;
    cy: number;
    midAngle: number;
    innerRadius: number;
    outerRadius: number;
    value: number;
} {
    if (typeof payload !== 'object' || payload === null) {
        return false;
    }

    const item = payload as Record<string, unknown>;

    return (
        typeof item.cx === 'number' &&
        typeof item.cy === 'number' &&
        typeof item.midAngle === 'number' &&
        typeof item.innerRadius === 'number' &&
        typeof item.outerRadius === 'number' &&
        typeof item.value === 'number'
    );
}

function ReportEmptyState() {
    return (
        <Card>
            <CardContent className="flex flex-col items-center gap-3 p-10 text-center">
                <div className="rounded-full bg-blue-50 p-3 text-blue-700 dark:bg-blue-950/40 dark:text-blue-200">
                    <BarChart3 className="size-6" />
                </div>
                <div>
                    <h2 className="font-semibold">
                        Belum ada analisis untuk periode ini
                    </h2>
                    <p className="mt-1 max-w-xl text-sm text-muted-foreground">
                        Pilih periode transaksi dan parameter analisis untuk
                        melihat ringkasan Apriori Lilin dan Pisau.
                    </p>
                </div>
                <Button type="submit" form="run-apriori-analysis">
                    Mulai Analisis
                </Button>
            </CardContent>
        </Card>
    );
}

function findRulePair(
    rules: Rule[],
    antecedent: string,
    consequent: string,
): Rule | null {
    return (
        rules.find(
            (rule) =>
                hasExactRuleSide(rule, 'antecedent', antecedent) &&
                hasExactRuleSide(rule, 'consequent', consequent),
        ) ?? null
    );
}

function hasExactRuleSide(
    rule: Rule,
    side: 'antecedent' | 'consequent',
    name: string,
): boolean {
    const names = ruleItems(rule, side);

    return names.length === 1 && names[0].toLowerCase() === name.toLowerCase();
}

function formatDuration(milliseconds: number | null): string {
    if (milliseconds === null) {
        return '-';
    }

    return `${(milliseconds / 1000).toLocaleString('id-ID', {
        maximumFractionDigits: 2,
    })} detik`;
}

function normalizeLift(lift: string | number): number {
    return clampRatio(Number(lift) / 2);
}

function clampRatio(value: number): number {
    return Math.max(0, Math.min(value, 1));
}
