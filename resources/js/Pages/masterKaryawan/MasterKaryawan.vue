<script setup>
import Layout from '../../Layout.vue'
import { useDataTable } from '@/composables/useDataTable'
import { Head } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { Modal } from 'bootstrap'
import { watch } from 'vue'
import { onMounted } from 'vue'

//reset
const initialForm = {
    nik: '',
    nik_lama: '',
    nama: '',
    gudang: '',
    bagian: '',
    kelas: '',
    jabatan: '',
    tipe: '',
    status_kerja: '',
    status_karyawan: '',
    jobclass: '',
    tgl_efektif: '',
    tgl_tetap: '',
    tgl_keluar: '',
    ket_masuk: '',
    ket_keluar: ''
}

const form = ref({ ...initialForm })

const daftarJabatan = {
    ADMIN: ['PHARMACIST', 'BPB SUPPLIER & NPB STORE', 'INVENTORY', 'TECHNICAL SUPPORT', 'CCTV' ],
    RECEIVING: ['SUPPLIER'],
    RETUR: ['RETUR TOKO IDM', 'SARANA DCI', 'RETUR SUPPLIER', 'ADM RETUR'],
    PERISHABLE: ['FRUIT AND CHILLED FOOD', 'SPECIAL PRODUCTS', 'BAKERY'],
    WAREHOUSE: ['FRACTION', 'BULKY', 'OTHERS', 'RENTAL WAREHOUSE', 'PICKER', 'SCANNER', 'HELPER', 'SECOND CHECKER', 'COURIER COORDINATOR', 'FACILITIES COORDINATOR', 'UTILITY OPERATOR', 'STOCK OPNAME'],
    ISSUINGDELVIERY: ['HELPER', 'LOADING', 'ADM DELIVERY', 'CHIEF DRIVER', 'DELIVERY SUPPORT', 'GPS', 'DRIVER DRY', 'DRIVER NON DRY'],
    WAREHOUSEKLIK: ['KLIK INDOMARET', 'PICKER', 'SCANNER']
}

const jabatanOptions = computed(() => {
    return daftarJabatan[form.value.bagian] || []
})

//Reset jabatan saat bagian berubah
watch(() => form.value.bagian, () => {
    form.value.jabatan = ''
})

//Reset form
const resetForm = () => {
    form.value = { ...initialForm }
}

const fillForm = (row) => {
    if (!row) return

    form.value = {
        ...initialForm,
        ...row
    }
}

const showModal = () => {
    const modalEl = document.getElementById('modal-report')
    const modal = new Modal(modalEl)

    modalEl.addEventListener('hidden.bs.modal', () => {
        resetForm()
    })

    modal.show()
}

const user = computed(() => usePage().props.auth.user)

defineProps({ user: Object })

const { getTable } = useDataTable(
    'masterKaryawanTable',
    '/masterKaryawan/data',

        [
            {
                data: null,
                name: 'no',
                orderable: false,
                searchable: false,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },  
            { data: 'nik', name: 'nik' },
            { data: 'nama', name: 'nama' },
            { data: 'lokasi', name: 'lokasi' },
            { data: 'bagian', name: 'bagian' },
            { data: 'status_kerja', name: 'status_kerja' },
            { data: 'tgl_efektif', name: 'tgl_efektif' },
            { data: 'tgl_tetap', name: 'tgl_tetap' },
            {
                data: 'id',
                name: 'action',
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return `
                        <button class="btn btn-warning btn-sm detail-btn" data-id="${data}">
                            <i class="fa-solid fa-pen-to-square"></i> Edit
                        </button>
                        <button class="btn btn-warning btn-sm edit-btn" data-id="${data}">
                            <i class="fa-solid fa-pen-to-square"> </i> Edit
                        </button>
                        <button class="btn btn-danger btn-sm delete-btn" data-id="${data}">
                            <i class="fa-solid fa-trash"></i> Hapus
                        </button>
                    `;
                }
            },
        ],
    {
        dom:
            "<'row mb-3 align-items-center'<'col-md-4'l><'col-md-8 d-flex justify-content-end align-items-center gap-2'fB>>" +
            "<'row'<'col-12'tr>>" +
            "<'row mt-3 align-items-center'<'col-md-6'i><'col-md-6 d-flex justify-content-end'p>>",

        language: {
            paginate: {
                previous: "Previous",
                next: "Next"
            }
        },

        columnDefs: [
            {
                targets: "_all",
                className: "text-center"
            }
        ],

        buttons: [
            {
                extend: 'excel',
                text: 'Export Excel',
                className: 'btn btn-success btn-sm'
            },
            {
                extend: 'pdf',
                text: 'Export PDF',
                className: 'btn btn-danger btn-sm'
            }
        ]
    },
)

defineOptions({
  layout: Layout
})



onMounted(() => {
    $('#masterKaryawanTable tbody').on('click', '.edit-btn', function () {

        const table = getTable()
        if (!table) return

        const rowData = table.row($(this).closest('tr')).data()
        if (!rowData) return

        fillForm(rowData)
    })
})

</script>

<template>
    <!-- <Layout> -->
        <Head title="Master Karyawan" />
        <!-- BEGIN PAGE HEADER -->
        <!-- <div class="page-header d-print-none" aria-label="Page header">
            <div class="container-fluid">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        <h2 class="page-title">Master Karyawan</h2>
                    </div>
                    <div class="col-auto ms-auto d-print-none">
                        <div class="btn-list">
                            <a href="#" class="btn btn-primary btn-5 d-none d-sm-inline-block"
                            @click.prevent="showModal">
                            <i class="fa-solid fa-plus"> </i> Buat Baru
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
        <!-- END PAGE HEADER -->

        <div class="page-body">
            <div class="container-fluid">
                <div class="row row-deck row-cards">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header d-flex align-items-center">
                                <h3 class="card-title mb-0">Data Karyawan</h3>

                                <div class="ms-auto d-flex gap-2">
                                    <a href="#"
                                    class="btn btn-sm btn-warning"
                                    @click="resetForm">
                                        Reset
                                    </a>

                                    <a href="#"
                                    class="btn btn-sm btn-primary"
                                    @click.prevent="showModal">
                                        <i class="fa-solid fa-plus"></i> Buat Baru
                                    </a>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="row align-items-stretch">

                                    <!-- KOLOM KIRI -->
                                    <div class="col-md-4 d-flex">
                                        <div class="border w-100 d-flex justify-content-center align-items-center text-secondary">
                                            <svg viewBox="0 0 24 24" width="70%" height="70%" fill="gray">
                                                <circle cx="12" cy="7" r="4"></circle>
                                                <path d="M4 21c0-4 4-6 8-6s8 2 8 6"></path>
                                            </svg>
                                        </div>
                                    </div>

                                    <!-- KOLOM KANAN -->
                                    <div class="col-md-8 d-flex">
                                        <div class="w-100">
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">NIK</label>
                                                    <input type="text" v-model="form.nik" class="form-control">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">NIK Lama</label>
                                                    <input type="text" v-model="form.nik_lama" class="form-control">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-8">
                                                    <label class="form-label">Nama</label>
                                                    <input type="text" v-model="form.nama" @input="form.nama = form.nama.toUpperCase()" class="form-control">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Gudang</label>
                                                    <select v-model="form.gudang" class="form-select">
                                                        <option value="" disabled>-Pilih Gudang-</option>
                                                        <option value="DCI">DCI</option>
                                                        <option value="DEPO 1">DEPO 1</option>
                                                        <option value="DEPO 2">DEPO 2</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-4">
                                                    <label class="form-label">Bagian</label>
                                                    <select v-model="form.bagian" class="form-select">
                                                        <option value="" disabled>-Pilih Bagian-</option>
                                                        <option value="ADMIN">ADMIN</option>
                                                        <option value="RECEIVING">RECEIVING</option>
                                                        <option value="RETUR">RETUR</option>
                                                        <option value="PERISHABLE">PERISHABLE</option>
                                                        <option value="WAREHOUSE">WAREHOUSE</option>
                                                        <option value="WAREHOUSEKLIK">WAREHOUSE KLIK</option>
                                                        <option value="ISSUINGDELVIERY">ISSUING DELIVERY</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Kelas</label>
                                                    <select v-model="form.kelas" class="form-select">
                                                        <option value="" disabled>-Pilih Kelas-</option>
                                                        <option value="SUPERVISOR">SUPERVISOR</option>
                                                        <option value="SR. CLERK">SR. CLERK</option>
                                                        <option value="CLERK">CLERK</option>
                                                        <option value="COORDINATOR">COORDINATOR</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Jabatan</label>
                                                    <select v-model="form.jabatan"
                                                        class="form-select"
                                                        :disabled="form.bagian === ''">

                                                        <option value="" disabled>-Pilih Jabatan-</option>

                                                        <option 
                                                            v-for="item in jabatanOptions" 
                                                            :key="item" 
                                                            :value="item">
                                                            {{ item }}
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- <div class="row"> -->
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Status Kerja</label>
                                            <select v-model="form.status_kerja" class="form-select">
                                                <option value="" disabled>-Pilih Status Kerja-</option>
                                                <option>TETAP</option>
                                                <option>KONTRAK</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Status Karyawan</label>
                                            <select v-model="form.status_karyawan" class="form-select">
                                                <option value="" disabled>-Pilih Status Karyawan-</option>
                                                <option>AKTIF</option>
                                                <option>TIDAK AKTIF</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Tipe</label>
                                            <select v-model="form.tipe" class="form-select">
                                                <option value="" disabled>-Pilih Tipe-</option>
                                                <option>DRIVER</option>
                                                <option>NON DRIVER</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Jobclass</label>
                                            <select v-model="form.jobclass" class="form-select">
                                                <option value="" disabled>-Pilih Jobclass-</option>
                                                <option>A1</option>
                                                <option>A2</option>
                                                <option>A3</option>
                                                <option>B1</option>
                                                <option>B2</option>
                                                <option>B3</option>
                                                <option>C1</option>
                                                <option>C2</option>
                                                <option>C3</option>
                                            </select>
                                        </div>
                                    </div>
                                <!-- </div> -->
                                <!-- <div class="row"> -->
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Tanggal Efektif</label>
                                            <input type="date" class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Tanggal Tetap</label>
                                            <input type="date" class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Tanggal Keluar</label>
                                            <input type="date" class="form-control">
                                        </div>
                                    </div>
                                <!-- </div> -->
                                <!-- <div class="row"> -->
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Keterangan Masuk</label>
                                            <textarea v-model="form.ket_masuk" class="form-control" rows="3"></textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Keterangan Keluar</label>
                                            <textarea v-model="form.ket_keluar" class="form-control" rows="3"></textarea>
                                        </div>
                                    </div>
                                <!-- </div> -->
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="h2">Master Karyawan</h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12 col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="masterKaryawanTable" class="table table-vcenter table-striped">
                                        <thead>
                                            <tr>
                                                <th>No. </th>
                                                <th>NIK</th>
                                                <th>Nama</th>
                                                <th>Gudang</th>
                                                <th>Bagian</th>
                                                <th>Status Kerja</th>
                                                <th>Tanggal Efektif</th>
                                                <th>Tanggal Tetap</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- BEGIN MODAL -->
        <div class="modal modal-blur fade" id="modal-report" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title">Tambah Master Karyawan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">NIK</label>
                                    <input type="text" v-model="form.nik" class="form-control" placeholder="Masukkan NIK">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">NIK Lama</label>
                                    <input type="text" v-model="form.nik_lama"class="form-control" placeholder="Masukkan NIK Lama">
                                </div>
                            </div>
                            <div class="col-lg-8">
                                <div class="mb-3">
                                    <label class="form-label">Nama</label>
                                    <input type="text" v-model="form.nama" class="form-control" placeholder="Masukkan nama karyawan">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="mb-3">
                                    <label class="form-label">Gudang</label>
                                    <select v-model="form.gudang" class="form-select">
                                        <option value="" disabled>-Pilih Gudang-</option>
                                        <option value="DCI">DCI</option>
                                        <option value="DEPO 1">DEPO 1</option>
                                        <option value="DEPO 2">DEPO 2</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <label class="form-label">Bagian</label>
                                    <select v-model="form.bagian" class="form-select">
                                        <option value="" disabled>-Pilih Bagian-</option>
                                        <option value="ADMIN">ADMIN</option>
                                        <option value="RECEIVING">RECEIVING</option>
                                        <option value="RETUR">RETUR</option>
                                        <option value="PERISHABLE">PERISHABLE</option>
                                        <option value="WAREHOUSE">WAREHOUSE</option>
                                        <option value="WAREHOUSEKLIK">WAREHOUSE KLIK</option>
                                        <option value="ISSUINGDELVIERY">ISSUING DELIVERY</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <label class="form-label">Kelas</label>
                                    <select v-model="form.kelas" class="form-select">
                                        <option value="" disabled>-Pilih Kelas-</option>
                                        <option value="SUPERVISOR">SUPERVISOR</option>
                                        <option value="SR. CLERK">SR. CLERK</option>
                                        <option value="CLERK">CLERK</option>
                                        <option value="COORDINATOR">COORDINATOR</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <label class="form-label">Jabatan</label>
                                    <select v-model="form.jabatan"
                                        class="form-select"
                                        :disabled="form.bagian === ''">

                                        <option value="" disabled>-Pilih Jabatan-</option>

                                        <option 
                                            v-for="item in jabatanOptions" 
                                            :key="item" 
                                            :value="item">
                                            {{ item }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <label class="form-label">Tipe</label>
                                    <select v-model="form.tipe" class="form-select">
                                        <option value="" disabled>-Pilih Tipe-</option>
                                        <option>DRIVER</option>
                                        <option>NON DRIVER</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <div class="mb-3">
                                    <label class="form-label">Status Kerja</label>
                                    <select v-model="form.status_kerja" class="form-select">
                                        <option value="" disabled>-Pilih Status Kerja-</option>
                                        <option>TETAP</option>
                                        <option>KONTRAK</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <div class="mb-3">
                                    <label class="form-label">Status Karyawan</label>
                                    <select v-model="form.status_karyawan" class="form-select">
                                        <option value="" disabled>-Pilih Status Karyawan-</option>
                                        <option>AKTIF</option>
                                        <option>TIDAK AKTIF</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="mb-3">
                                    <label class="form-label">Jobclass</label>
                                    <select v-model="form.jobclass" class="form-select">
                                        <option value="" disabled>-Pilih Jobclass-</option>
                                        <option>A1</option>
                                        <option>A2</option>
                                        <option>A3</option>
                                        <option>B1</option>
                                        <option>B2</option>
                                        <option>B3</option>
                                        <option>C1</option>
                                        <option>C2</option>
                                        <option>C3</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Efektif</label>
                                    <input type="date" v-model="form.tgl_efektif" class="form-control">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Tetap</label>
                                    <input type="date" v-model="form.tgl_tetap" class="form-control">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Keluar</label>
                                    <input type="date" v-model="form.tgl_keluar" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Keterangan Masuk</label>
                                <textarea v-model="form.ket_masuk" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Keterangan Keluar</label>
                                <textarea v-model="form.ket_keluar" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="#" class="btn btn-link link-secondary btn-3" data-bs-dismiss="modal" @click="resetForm"> Cancel </a>
                        <button type="button" class="btn btn-warning" @click="resetForm">
                            Reset
                        </button>
                        <a href="#" class="btn btn-primary btn-5 ms-auto" data-bs-dismiss="modal">
                            Simpan
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- END MODAL -->

    <!-- </Layout> -->
</template>