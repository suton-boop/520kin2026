import { Link } from '@inertiajs/react';
import React from 'react';

export default function Pagination({ links }) {
    if (!links || links.length <= 3) return null;

    return (
        <div className="flex flex-wrap justify-center mt-6 mb-4 gap-1">
            {links.map((link, index) => {
                const label = link.label
                    .replace(/&laquo;/g, 'Back')
                    .replace(/&raquo;/g, 'Next')
                    .replace(/Previous/g, 'Back');

                return link.url === null ? (
                    <div
                        key={index}
                        className="px-4 py-2 text-sm text-gray-400 bg-gray-100 rounded-xl shadow-sm border border-gray-200 cursor-not-allowed font-medium"
                    >
                        <span dangerouslySetInnerHTML={{ __html: label }}></span>
                    </div>
                ) : (
                    <Link
                        key={index}
                        href={link.url}
                        className={`px-4 py-2 text-sm rounded-xl shadow-sm border font-medium transition-all duration-200 ${
                            link.active
                                ? 'bg-blue-900 text-white border-blue-900 font-bold'
                                : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 hover:text-blue-600'
                        }`}
                    >
                        <span dangerouslySetInnerHTML={{ __html: label }}></span>
                    </Link>
                );
            })}
        </div>
    );
}
