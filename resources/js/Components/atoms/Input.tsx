import { InputHTMLAttributes } from "react";

interface InputProps extends InputHTMLAttributes<HTMLInputElement> {
    className?: string;
}

export default function Input({ className = "", ...props }: InputProps) {
    return (
        <input
            className={`
                w-full
                rounded-md
                border border-slate-300
                px-3 py-2
                text-sm
                text-slate-700
                placeholder:text-slate-400
                focus:outline-none
                focus:ring-2
                focus:ring-slate-400
                ${className}
            `}
            {...props}
        />
    );
}
