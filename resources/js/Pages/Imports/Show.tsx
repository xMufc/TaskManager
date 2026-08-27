import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head } from "@inertiajs/react";
import ImportResultView from "@/Components/organisms/ImportResultView";
import { ImportResult } from "@/types/import";

interface Props {
    importResult: ImportResult;
}

export default function Show({ importResult }: Props) {
    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Wynik importu
                </h2>
            }
        >
            <Head title="Wynik importu" />
            <div className="mx-auto max-w-4xl p-6">
                <ImportResultView importResult={importResult} />
            </div>
        </AuthenticatedLayout>
    );
}
