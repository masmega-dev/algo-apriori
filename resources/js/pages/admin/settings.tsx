import { Head } from '@inertiajs/react';
import type { ComponentProps } from 'react';
import { SettingsPageContent } from '@/components/admin/settings-page-content';

export default function Settings(
    props: ComponentProps<typeof SettingsPageContent>,
) {
    return <><Head title="Pengaturan" /><SettingsPageContent {...props} /></>;
}
