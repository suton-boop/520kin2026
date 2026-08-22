import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link, useForm, router } from "@inertiajs/react";
import {
  PlusIcon,
  PencilSquareIcon,
  TrashIcon,
  UserGroupIcon,
  DocumentArrowUpIcon,
  CloudArrowUpIcon,
  DocumentArrowDownIcon,
  CheckCircleIcon,
  XCircleIcon,
} from "@heroicons/react/24/solid";

export default function Index({ auth, users, gugusMutus = [] }) {
  const { delete: destroy } = useForm();
  const {
    data: importData,
    setData: setImportData,
    post: postImport,
    processing,
    reset: resetImport,
    errors: importErrors,
  } = useForm({
    file: null,
    gugus_mutu_id: "",
  });

  const deleteUser = (e, id) => {
    e.preventDefault();
    if (confirm("Apakah Anda yakin ingin menghapus user ini?")) {
      destroy(route("users.destroy", id));
    }
  };

  const handleImport = (e) => {
    e.preventDefault();
    postImport(route("import.program"), {
      onSuccess: () => resetImport(),
    });
  };

  const toggleImportStatus = (id) => {
    if (confirm("Ubah status izin import untuk Gugus Mutu ini?")) {
      router.post(route("gugus-mutu.toggle-import", id));
    }
  };

  return (
    <AuthenticatedLayout>
      <Head title="Manajemen User - Dashboardkin 520" />

      <div className="py-12 bg-gray-50 flex-1 min-h-screen">
        <div className="mx-auto max-w-screen-2xl sm:px-6 lg:px-8 space-y-8">
          <div className="bg-white p-8 shadow-sm sm:rounded-3xl border border-gray-100 flex flex-col md:flex-row items-center justify-between">
            <div className="flex items-center space-x-6">
              <div className="bg-blue-900 p-4 rounded-2xl shadow-xl">
                <UserGroupIcon className="h-8 w-8 text-amber-400" />
              </div>
              <div>
                <h3 className="text-2xl font-black tracking-tighter text-blue-900 uppercase italic">
                  Administrasi Akun & Akses
                </h3>
                <p className="mt-1 text-xs text-gray-400 font-bold uppercase tracking-widest italic pt-1">
                  Kelola daftar pengguna, peran, dan grup gugus mutu di sini.
                </p>
              </div>
            </div>
            <div className="mt-6 md:mt-0">
              <Link
                href={route("users.create")}
                className="inline-flex items-center px-8 py-4 bg-blue-900 border border-transparent rounded-[20px] font-black text-xs text-white uppercase tracking-widest hover:bg-blue-950 transition shadow-2xl"
              >
                <PlusIcon className="w-5 h-5 mr-3 text-amber-400" /> Tambah User
                Baru
              </Link>
            </div>
          </div>

          <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div className="lg:col-span-2 space-y-8">
              {/* Excel Import Section */}
              <div className="bg-white p-8 shadow-sm sm:rounded-3xl border border-gray-100 bg-gradient-to-r from-blue-50/30 to-white">
                <div className="space-y-6">
                  <div className="flex items-center justify-between">
                    <div className="flex items-center space-x-4">
                      <div className="bg-amber-400 p-3 rounded-xl shadow-lg">
                        <CloudArrowUpIcon className="h-6 w-6 text-blue-900" />
                      </div>
                      <h4 className="text-lg font-black tracking-tighter text-blue-900 uppercase italic">
                        Import Program Per GM
                      </h4>
                    </div>
                    <a
                      href="/templates/template_import.xlsx"
                      target="_blank"
                      rel="noopener noreferrer"
                      className="text-[10px] font-black text-blue-700 uppercase tracking-widest hover:underline"
                    >
                      Unduh Template (.xlsx)
                    </a>
                  </div>

                  <form
                    onSubmit={handleImport}
                    className="flex flex-col sm:flex-row items-end gap-3"
                  >
                    <div className="w-full sm:w-1/2 space-y-1">
                      <label className="text-[9px] font-black text-blue-900/60 uppercase ml-1">
                        Pilih Gugus Mutu
                      </label>
                      <select
                        className="w-full h-12 bg-gray-50 border-gray-100 rounded-xl text-[11px] font-bold text-gray-700 focus:ring-amber-400 focus:border-amber-400 transition-all uppercase italic"
                        value={importData.gugus_mutu_id}
                        onChange={(e) =>
                          setImportData("gugus_mutu_id", e.target.value)
                        }
                        required
                      >
                        <option value="">-- PILIH GM --</option>
                        {gugusMutus.map((gm) => (
                          <option key={gm.id} value={gm.id}>
                            {gm.name}
                          </option>
                        ))}
                      </select>
                    </div>

                    <div className="w-full sm:w-1/2 space-y-1">
                      <label className="text-[9px] font-black text-blue-900/60 uppercase ml-1">
                        File Excel
                      </label>
                      <input
                        type="file"
                        onChange={(e) => setImportData("file", e.target.files[0])}
                        className="w-full h-12 bg-gray-50 border-gray-100 rounded-xl text-[10px] p-2"
                        required
                      />
                    </div>

                    <button
                      type="submit"
                      disabled={processing}
                      className="h-12 px-6 bg-blue-900 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-950 transition-all disabled:opacity-50 shadow-lg"
                    >
                      IMPORT
                    </button>
                  </form>
                </div>
              </div>

              {/* Users Table */}
              <div className="bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-gray-100">
                <div className="overflow-x-auto">
                  <table className="w-full text-sm text-left border-collapse">
                    <thead className="bg-blue-900 text-white font-black uppercase text-[10px] tracking-widest italic">
                      <tr>
                        <th className="px-6 py-6 font-black italic">User</th>
                        <th className="px-6 py-6 font-black italic">Peran</th>
                        <th className="px-6 py-6 font-black italic">
                          Gugus Mutu
                        </th>
                        <th className="px-6 py-6 text-center font-black italic">
                          Aksi
                        </th>
                      </tr>
                    </thead>
                    <tbody className="bg-white divide-y divide-gray-100">
                      {users.data && users.data.length > 0 ? (
                        users.data.map((user) => (
                          <tr
                            key={user.id}
                            className="hover:bg-blue-50/20 transition-all group"
                          >
                            <td className="px-6 py-5 whitespace-nowrap">
                              <div className="flex items-center">
                                <div className="h-8 w-8 rounded-lg bg-gray-100 flex items-center justify-center text-blue-900 font-black text-[10px]">
                                  {user.name.charAt(0)}
                                </div>
                                <div className="ml-3">
                                  <p className="font-black text-gray-900 text-[12px] uppercase tracking-tighter">
                                    {user.name}
                                  </p>
                                  <p className="text-[9px] text-gray-400 font-bold uppercase">
                                    {user.email}
                                  </p>
                                </div>
                              </div>
                            </td>
                            <td className="px-6 py-5 whitespace-nowrap">
                              {user.roles && user.roles.length > 0 ? (
                                <span className="px-2 py-0.5 bg-amber-50 text-amber-600 border border-amber-100 rounded text-[9px] font-black uppercase">
                                  {user.roles[0].name}
                                </span>
                              ) : (
                                "-"
                              )}
                            </td>
                            <td className="px-6 py-5 whitespace-nowrap">
                              <span className="text-[10px] font-black text-blue-800 uppercase italic">
                                {user.gugus_mutu?.name || "UMUM"}
                              </span>
                            </td>
                            <td className="px-6 py-5 whitespace-nowrap text-center space-x-2">
                              <Link
                                href={route("users.edit", user.id)}
                                className="inline-flex p-2 bg-blue-50 text-blue-900 rounded-lg hover:bg-blue-900 hover:text-white transition-all shadow-sm"
                              >
                                <PencilSquareIcon className="h-4 w-4" />
                              </Link>
                              {user.id !== auth.user.id && (
                                <button
                                  onClick={(e) => deleteUser(e, user.id)}
                                  className="inline-flex p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-all shadow-sm"
                                >
                                  <TrashIcon className="h-4 w-4" />
                                </button>
                              )}
                            </td>
                          </tr>
                        ))
                      ) : (
                        <tr>
                          <td colSpan="4" className="px-6 py-10 text-center">
                            NIHIL
                          </td>
                        </tr>
                      )}
                    </tbody>
                  </table>
                        </div>
                        {users && users.links && <Pagination links={users.links} />}
              </div>
            </div>

            <div className="space-y-8">
              {/* Gugus Mutu Status Toggle Section */}
              <div className="bg-blue-900 p-8 shadow-sm sm:rounded-3xl border border-gray-100 text-white">
                <h4 className="text-lg font-black tracking-tighter uppercase italic border-b border-blue-800 pb-4 mb-6">
                  Status Izin Import
                </h4>
                <div className="space-y-4">
                  {gugusMutus.map((gm) => (
                    <div
                      key={gm.id}
                      className="flex items-center justify-between p-4 bg-blue-950/50 rounded-2xl border border-blue-800 shadow-inner group hover:bg-blue-950 transition-all"
                    >
                      <div>
                        <p className="text-[11px] font-black uppercase italic tracking-widest text-amber-400">
                          {gm.name}
                        </p>
                        <p className="text-[9px] font-bold uppercase text-blue-300 mt-1">
                          {gm.allow_import ? "IMPORT AKTIF" : "IMPORT MATI"}
                        </p>
                      </div>
                      <button
                        onClick={() => toggleImportStatus(gm.id)}
                        className={`p-2 rounded-xl border transition-all ${
                          gm.allow_import
                            ? "bg-amber-400 text-blue-900 border-amber-300 shadow-[0_0_15px_rgba(251,191,36,0.3)]"
                            : "bg-blue-900/50 text-blue-300 border-blue-800"
                        }`}
                      >
                        {gm.allow_import ? (
                          <CheckCircleIcon className="h-6 w-6" />
                        ) : (
                          <XCircleIcon className="h-6 w-6 opacity-30" />
                        )}
                      </button>
                    </div>
                  ))}
                </div>
                <div className="mt-8 p-4 bg-amber-400/10 rounded-2xl border border-amber-400/20">
                  <p className="text-[9px] font-black text-amber-400 uppercase italic leading-relaxed">
                    💡 Aktifkan tombol (kuning) di tiap GM agar Staff/Manager
                    pada GM tersebut bisa melakukan import program mandiri.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
