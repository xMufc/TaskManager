import { SelectHTMLAttributes } from "react";

interface SelectProps extends SelectHTMLAttributes<HTMLSelectElement> {
    label?: string;
    className?: string;
}

export default function Select({
    label,
    className = "",
    children,
    ...props
}: SelectProps) {
    return (
        <div className="flex flex-col gap-1">
            {label && (
                <label className="text-sm font-medium text-slate-700">
                    {label}
                </label>
            )}

            <select
                className={`
                    w-full
                    rounded-md
                    border border-slate-300
                    bg-white
                    px-3 py-2
                    text-sm
                    text-slate-700
                    focus:outline-none
                    focus:ring-2
                    focus:ring-slate-400
                    disabled:cursor-not-allowed
                    disabled:bg-slate-100
                    ${className}
                `}
                {...props}
            >
                {children}
            </select>
        </div>
    );
}
