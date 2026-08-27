import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link } from "@inertiajs/react";
import { AiFillCheckSquare, AiFillCloseSquare } from "react-icons/ai";

interface ImportSummary {
    id: string;
    status: "processing" | "completed";
    acceptedCount: number;
    rejectedCount: number;
    createdAt: string;
}

interface Props {
    imports: ImportSummary[];
}

export default function Index({ imports }: Props) {
    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Historia importów
                </h2>
            }
        >
            <Head title="Historia importów" />

            <div className="mx-auto max-w-4xl p-6">
                {imports.length === 0 ? (
                    <p className="text-sm text-slate-500">
                        Brak dotychczasowych importów.
                    </p>
                ) : (
                    <ul className="divide-y rounded-md border border-slate-200 bg-white">
                        {imports.map((imp) => (
                            <li key={imp.id}>
                                <Link
                                    href={route("imports.show", imp.id)}
                                    className="flex items-center justify-between px-4 py-3 hover:bg-slate-50"
                                >
                                    <span className="text-sm text-slate-600">
                                        {imp.createdAt}
                                    </span>
                                    <span className="flex items-center gap-3 text-sm">
                                        {imp.status === "processing" ? (
                                            <span className="text-amber-600">
                                                W trakcie...
                                            </span>
                                        ) : (
                                            <>
                                                <span className="text-emerald-600">
                                                    {AiFillCheckSquare({
                                                        className:
                                                            "inline-block h-4 w-4",
                                                    })}
                                                    {imp.acceptedCount}
                                                </span>
                                                <span className="text-rose-600">
                                                    {AiFillCloseSquare({
                                                        className:
                                                            "inline-block h-4 w-4",
                                                    })}
                                                    {imp.rejectedCount}
                                                </span>
                                            </>
                                        )}
                                    </span>
                                </Link>
                            </li>
                        ))}
                    </ul>
                )}
            </div>
        </AuthenticatedLayout>
    );
}
