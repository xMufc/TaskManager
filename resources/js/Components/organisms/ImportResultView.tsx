import { ImportResult } from "@/types/import";
import { useImportPolling } from "@/Hooks/useImportPolling";

export default function ImportResultView({
    importResult,
}: {
    importResult: ImportResult;
}) {
    useImportPolling(importResult);

    if (importResult.status === "processing") {
        return (
            <div className="flex items-center gap-2 rounded-md bg-slate-50 px-4 py-3 text-sm text-slate-600">
                <span className="h-2 w-2 animate-pulse rounded-full bg-amber-500" />
                Import w trakcie przetwarzania...
            </div>
        );
    }

    const { accepted, rejected } = importResult;

    return (
        <div className="space-y-6">
            <div className="flex gap-4">
                <div className="rounded-md bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    Zaakceptowane:{" "}
                    <span className="font-medium font-bold">
                        {accepted.length}
                    </span>
                </div>
                <div className="rounded-md bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    Odrzucone:{" "}
                    <span className="font-medium font-bold">
                        {rejected.length}
                    </span>
                </div>
            </div>

            {accepted.length > 0 && (
                <div>
                    <h3 className="mb-2 text-sm font-semibold text-slate-700">
                        Zaimportowane zadania
                    </h3>
                    <ul className="divide-y rounded-md border border-slate-200">
                        {accepted.map((row) => (
                            <li
                                key={row.row}
                                className="flex justify-between px-4 py-2 text-sm"
                            >
                                <span className="text-slate-500">
                                    Wiersz {row.row}
                                </span>
                                <span className="text-slate-900">
                                    {row.title}
                                </span>
                            </li>
                        ))}
                    </ul>
                </div>
            )}

            {rejected.length > 0 && (
                <div>
                    <h3 className="mb-2 text-sm font-semibold text-slate-700">
                        Odrzucone wiersze
                    </h3>
                    <ul className="divide-y rounded-md border border-rose-200">
                        {rejected.map((row) => (
                            <li
                                key={row.row}
                                className="flex justify-between px-4 py-2 text-sm"
                            >
                                <span className="text-slate-500">
                                    Wiersz {row.row}
                                </span>
                                <span className="text-rose-600">
                                    {row.reason}
                                </span>
                            </li>
                        ))}
                    </ul>
                </div>
            )}
        </div>
    );
}
