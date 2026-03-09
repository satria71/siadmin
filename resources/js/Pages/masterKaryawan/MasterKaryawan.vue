<script setup>
import Layout from '../../Layout.vue'
import { useDataTable } from '@/Composables/useDataTable'
import { Head } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { Modal } from 'bootstrap'
import { watch } from 'vue'
import { onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import Breadcrumb from '@/Components/Breadcrumb.vue'
import FormInput from '@/Components/FormInput.vue'
import FormSelect from '@/Components/FormSelect.vue'
import FormTextarea from '@/Components/FormTextarea.vue'
import Swal from 'sweetalert2'

const isEdit = ref(false)
const selectedId = ref(null)

const breadcrumbs = [
  { label: 'Dashboard', url: '/panel' },
  { label: 'Data Karyawan', url: 'masterKaryawan' },
]

defineProps({
    auth: Object,
    errors: Object
})


//reset
const form = useForm({
    nik: '',
    nik_lama: '',
    nama: '',
    gudang: '',
    bagian: '',
    kelas: '',
    jabatan: '',
    jabatan_ho: '',
    tipe: '',
    status_kerja: '',
    status_karyawan: '',
    job_class: '',
    tgl_efektif: '',
    tgl_tetap: '',
    tgl_keluar: '',
    ket_masuk: '',
    ket_keluar: ''
})

// const form = ref({ ...initialForm })

const daftarJabatan = {
    ADMINISTRATION: ['PHARMACIST', 'BPB SUPPLIER & NPB STORE', 'INVENTORY', 'TECHNICAL SUPPORT', 'CCTV' ],
    RECEIVING: ['SUPPLIER'],
    RETUR: ['RETUR TOKO IDM', 'SARANA DCI', 'RETUR SUPPLIER', 'ADM RETUR'],
    PERISHABLE: ['FRUIT AND CHILLED FOOD', 'SPECIAL PRODUCTS', 'BAKERY'],
    WAREHOUSE: ['FRACTION', 'BULKY', 'OTHERS', 'RENTAL WAREHOUSE', 'PICKER', 'SCANNER', 'HELPER', 'SECOND CHECKER', 'COURIER COORDINATOR', 'FACILITIES COORDINATOR', 'UTILITY OPERATOR', 'STOCK OPNAME'],
    ISSUINGDELVIERY: ['HELPER', 'LOADING', 'ADM DELIVERY', 'CHIEF DRIVER', 'DELIVERY SUPPORT', 'GPS', 'DRIVER DRY', 'DRIVER NON DRY'],
    WAREHOUSEKLIK: ['KLIK INDOMARET', 'PICKER', 'SCANNER']
}

const daftarJabatanHo = {
    ADMINISTRATION: ['PHARMACIST', 'BPB SUPPLIER & NPB STORE', 'INVENTORY', 'TECHNICAL SUPPORT', 'CCTV' ],
    RECEIVING: ['SUPPLIER'],
    RETUR: ['RETUR TOKO IDM', 'SARANA DCI', 'RETUR SUPPLIER', 'ADM RETUR'],
    PERISHABLE: ['FRUIT AND CHILLED FOOD', 'SPECIAL PRODUCTS', 'BAKERY'],
    WAREHOUSE: ['FRACTION', 'BULKY', 'OTHERS', 'RENTAL WAREHOUSE', 'PICKER', 'SCANNER', 'HELPER', 'SECOND CHECKER', 'COURIER COORDINATOR', 'FACILITIES COORDINATOR', 'UTILITY OPERATOR', 'STOCK OPNAME'],
    ISSUINGDELVIERY: ['HELPER', 'LOADING', 'ADM DELIVERY', 'CHIEF DRIVER', 'DELIVERY SUPPORT', 'GPS', 'DRIVER DRY', 'DRIVER NON DRY'],
    WAREHOUSEKLIK: ['KLIK INDOMARET', 'PICKER', 'SCANNER']
}

const daftarKelas = {
    ADMINISTRATION: ['SUPERVISOR','SR. CLERK','CLERK'],
    RECEIVING: ['SUPERVISOR','CHECKER','HELPER'],
    RETUR: ['SUPERVISOR','CLERK','HELPER'],
    PERISHABLE: ['SUPERVISOR','CHECKER','HELPER'],
    WAREHOUSE: ['SUPERVISOR','COORDINATOR','CHECKER','HELPER'],
    ISSUINGDELIVERY: ['SUPERVISOR','COORDINATOR','DRIVER','HELPER'],
    WAREHOUSEKLIK: ['SUPERVISOR','CHECKER','HELPER']
}

const jabatanOptions = computed(() => {
    return daftarJabatan[form.bagian] || []
})

const kelasOptions = computed(() => {
    return daftarKelas[form.bagian] || []
})

const jabatanHoOptions = computed(() => {
    return daftarJabatanHo[form.bagian] || []
})

//Reset jabatan saat bagian berubah
watch(() => form.bagian, () => {

    if (!isEdit.value) {
        form.jabatan = ''
        form.kelas = ''
        form.jabatan_ho = ''
    }

})

//error hilang saat user mengetik
const clearErrorOnInput = (field) => {
    watch(() => form[field], () => {
        form.clearErrors(field)
    })
}

clearErrorOnInput('nik')
clearErrorOnInput('nama')
clearErrorOnInput('gudang')
clearErrorOnInput('bagian')
clearErrorOnInput('kelas')
clearErrorOnInput('jabatan')
clearErrorOnInput('jabatan_ho')
clearErrorOnInput('tipe')
clearErrorOnInput('status_kerja')
clearErrorOnInput('status_karyawan')
clearErrorOnInput('tgl_efektif')
clearErrorOnInput('job_class')


//Reset form
const resetForm = () => {
    form.reset()
    form.clearErrors()
    isEdit.value = false
    selectedId.value = null
}

const fillForm = (row) => {

    Object.assign(form, row)

    selectedId.value = row.id
    isEdit.value = true

    showModal()
}

const showModal = () => {

    if (!isEdit.value) {
        resetForm()
    }

    const modalEl = document.getElementById('modal-report')
    const modal = new Modal(modalEl)

    modal.show()
}

const user = computed(() => usePage().props.auth.user)

// defineProps({ user: Object })

const { getTable } = useDataTable(
    'setTable',
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
            { data: 'gudang', name: 'gudang' },
            { data: 'bagian', name: 'bagian' },
            { data: 'status_kerja', name: 'status_kerja',
                render: function (data, type, row) {

                if (type === 'display') {

                    if (data?.trim().toUpperCase() === 'AKTIF') {
                        return `<span class="badge bg-green text-green-fg">AKTIF</span>`;
                    }

                    if (data?.trim().toUpperCase() === 'NON AKTIF') {
                        return `<span class="badge bg-red text-red-fg">NON AKTIF</span>`;
                    }

                    return `<span class="badge bg-secondary">${data ?? '-'}</span>`;
                }

                return data;
                }
            },
            { data: 'tgl_efektif', name: 'tgl_efektif' },
            { data: 'tgl_tetap', name: 'tgl_tetap' },
            {
                data: 'id',
                name: 'action',
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return `
                        <button class="btn btn-info btn-sm detail-btn" data-id="${data}">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                        <button class="btn btn-warning btn-sm edit-btn" data-id="${data}">
                            <i class="fa-solid fa-pen-to-square"> </i>
                        </button>
                        <button class="btn btn-danger btn-sm delete-btn" data-id="${data}">
                            <i class="fa-solid fa-trash"></i>
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
                text: `
                    <svg xmlns="http://www.w3.org/2000/svg" 
                        width="18" height="18" 
                        viewBox="0 0 24 24" 
                        fill="none" 
                        stroke="currentColor" 
                        stroke-width="2" 
                        stroke-linecap="round" 
                        stroke-linejoin="round" 
                        class="me-1">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                        <path d="M5 12v-7a2 2 0 0 1 2 -2h7l5 5v4" />
                        <path d="M4 15l4 6" />
                        <path d="M4 21l4 -6" />
                        <path d="M17 20.25c0 .414 .336 .75 .75 .75h1.25a1 1 0 0 0 1 -1v-1a1 1 0 0 0 -1 -1h-1a1 1 0 0 1 -1 -1v-1a1 1 0 0 1 1 -1h1.25a.75 .75 0 0 1 .75 .75" />
                        <path d="M11 15v6h3" />
                    </svg>
                `,
                className: 'btn btn-success text-white btn-sm d-inline-flex align-items-center gap-1'
            },
            {
                extend: 'pdf',
                text: `
                    <svg xmlns="http://www.w3.org/2000/svg"
                        width="18" height="18"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="me-1">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                        <path d="M5 12v-7a2 2 0 0 1 2 -2h7l5 5v4" />
                        <path d="M5 18h1.5a1.5 1.5 0 0 0 0 -3h-1.5v6" />
                        <path d="M17 18h2" />
                        <path d="M20 15h-3v6" />
                        <path d="M11 15v6h1a2 2 0 0 0 2 -2v-2a2 2 0 0 0 -2 -2h-1" />
                    </svg>
                `,
                // className: 'btn btn-danger btn-sm'
                className: 'btn btn-danger text-white btn-sm d-inline-flex align-items-center gap-1'
            }
        ]
    },
)

onMounted(() => {

    const modalEl = document.getElementById('modal-report')

    modalEl.addEventListener('hidden.bs.modal', () => {
        resetForm()
    })

    // DETAIL BUTTON
    $('#setTable tbody').on('click', '.detail-btn', function () {

        const id = $(this).data('id')

        if (!id) return

        router.visit(`/masterKaryawan/${id}`)
    })

    // EDIT BUTTON
    $('#setTable tbody').on('click', '.edit-btn', function () {

        const table = getTable()
        if (!table) return

        const rowData = table.row($(this).closest('tr')).data()
        if (!rowData) return

        fillForm(rowData)

    })

    // HAPUS BUTTON
    $('#setTable tbody').on('click', '.delete-btn', function () {

    const id = $(this).data('id')

        Swal.fire({
            title: 'Yakin hapus data?',
            text: "Data tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {

            if (result.isConfirmed) {

                router.delete(`/masterKaryawan/delete/${id}`, {
                    onSuccess: () => {

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Data berhasil dihapus',
                            timer: 1500,
                            showConfirmButton: false
                        })
                        // Swal.fire({
                        //     toast: true,
                        //     position: 'top-end',
                        //     icon: 'success',
                        //     title: 'Data berhasil dihapus',
                        //     showConfirmButton: false,
                        //     timer: 2500
                        // })

                        $('#setTable').DataTable().ajax.reload()
                    }
                })

            }

        })
    })
})

const submitForm = () => {

    const url = isEdit.value
        ? `/masterKaryawan/update/${selectedId.value}`
        : '/masterKaryawan/store'

    const method = isEdit.value ? 'put' : 'post'

    form[method](url, {
        onSuccess: () => {
            // SweetAlert
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: isEdit.value 
                    ? 'Data karyawan berhasil diperbarui'
                    : 'Data karyawan berhasil ditambahkan',
                // timer: 2000,
                showConfirmButton: true
            })

            // Swal.fire({
            //     toast: true,
            //     position: 'top-end',
            //     icon: 'success',
            //     title: isEdit.value
            //         ? 'Data Karyawan diperbarui'
            //         : 'Data karyawan ditambahkan',
            //     showConfirmButton: false,
            //     timer: 2500,
            //     timerProgressBar: true
            // })

            form.reset()

            $('#setTable').DataTable().ajax.reload()

            const modalEl = document.getElementById('modal-report')
            const modal = Modal.getInstance(modalEl)
            modal.hide()
        },
        onError: (errors) => {

            const firstError = Object.keys(errors)[0]

            const el = document.querySelector(`[name="${firstError}"]`)

            if (el) {
                el.focus()
                el.scrollIntoView({ behavior: 'smooth', block: 'center' })
            }

            Swal.fire({
                icon: 'error',
                title: 'Form belum lengkap',
                text: 'Periksa kembali data yang wajib diisi'
            })
        }
    })
}

defineOptions({
  layout: Layout
})

</script>

<template>
    <!-- <Layout> -->
        <Head title="Master Karyawan" />
        <!-- BEGIN PAGE HEADER -->
        <div class="page-header d-print-none" aria-label="Page header">
            <div class="container-fluid">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        <h2 class="page-title">Master Karyawan</h2>
                    </div>
                    <div class="col-auto ms-auto d-print-none">
                        <div class="btn-list">
                            <a href="#" class="btn btn-sm btn-primary btn-5 d-none d-sm-inline-block"
                            @click.prevent="showModal">
                            <i class="fa-solid fa-plus"> </i> Buat Baru
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-auto"><br>
                    <Breadcrumb :items="breadcrumbs" />
                </div>
            </div>
        </div>
        <!-- END PAGE HEADER -->

        <div class="page-body">
            <div class="container-fluid">
                
                <div class="row row-deck row-cards">
                    <div class="col-sm-12 col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-header">
                                    <div class="col">
                                        <h3 class="card-title mb-0">Data Master Karyawan</h3>
                                        <!-- <p class="text-secondary m-0">Monitoring data fraud admin</p> -->
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <br>
                                    <table id="setTable" class="table table-vcenter table-striped">
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
                        <h5 class="modal-title">
                            {{ isEdit ? 'Edit Master Karyawan' : 'Tambah Master Karyawan' }}
                        </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <!-- <label class="form-label">NIK</label>
                                    <input type="text" name="nik" v-model="form.nik" class="form-control" :class="{ 'is-invalid': form.errors.nik }" placeholder="Masukkan NIK">
                                    <div v-if="form.errors.nik" class="invalid-feedback">
                                        {{ form.errors.nik }}
                                    </div> -->
                                    <FormInput label="NIK" name="nik" v-model="form.nik" :error="form.errors.nik" placeholder="Masukkan NIK"/>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <FormInput label="NIK Lama" name="nik_lama" v-model="form.nik_lama" :error="form.errors.nik_lama" placeholder="Masukkan NIK Lama"/>
                                </div>
                            </div>
                            <div class="col-lg-8">
                                <div class="mb-3">
                                    <FormInput label="Nama" name="nama" v-model="form.nama" :error="form.errors.nama" placeholder="Masukkan Nama"/>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="mb-3">
                                    <FormSelect label="Gudang" name="gudang" v-model="form.gudang" :options="['DCI','DEPO 1','DEPO 2','WHK']" :error="form.errors.gudang" placeholder="-Pilih Gudang-"/>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <FormSelect 
                                    label="Bagian" name="bagian" v-model="form.bagian" 
                                    :options="['ADMINISTRATION','RECEIVING','RETUR','PERISHABLE','WAREHOUSE','WAREHOUSEKLIK','ISSUINGDELIVERY']" 
                                    :error="form.errors.bagian" placeholder="-Pilih Bagian-"/>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="mb-3">
                                <label class="form-label">Kelas</label>
                                    <select
                                        name="kelas"
                                        v-model="form.kelas"
                                        class="form-select"
                                        :disabled="form.bagian === ''"
                                        :class="{ 'is-invalid': form.errors.kelas }">

                                        <option value="" disabled>-Pilih Kelas-</option>

                                        <option 
                                            v-for="item in kelasOptions"
                                            :key="item"
                                            :value="item">
                                            {{ item }}
                                        </option>
                                        <div v-if="form.errors.kelas" class="invalid-feedback">
                                            {{ form.errors.kelas }}
                                        </div>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <label class="form-label">Jabatan DC</label>
                                    <select name="jabatan" v-model="form.jabatan"
                                        class="form-select"
                                        :disabled="form.bagian === ''"
                                        :class="{ 'is-invalid': form.errors.jabatan }">

                                        <option value="" disabled>-Pilih Jabatan-</option>

                                        <option 
                                            v-for="item in jabatanOptions" 
                                            :key="item" 
                                            :value="item">
                                            {{ item }}
                                        </option>
                                        
                                    </select>
                                    <div v-if="form.errors.jabatan" class="invalid-feedback">
                                        {{ form.errors.jabatan }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <label class="form-label">Jabatan HO</label>
                                    <select name="jabatan" v-model="form.jabatan_ho"
                                        class="form-select"
                                        :disabled="form.bagian === ''"
                                        :class="{ 'is-invalid': form.errors.jabatan_ho }">

                                        <option value="" disabled>-Pilih Jabatan-</option>

                                        <option 
                                            v-for="item in jabatanHoOptions" 
                                            :key="item" 
                                            :value="item">
                                            {{ item }}
                                        </option>
                                        
                                    </select>
                                    <div v-if="form.errors.jabatan_ho" class="invalid-feedback">
                                        {{ form.errors.jabatan_ho }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="mb-3">
                                    <FormSelect 
                                    label="Tipe" name="tipe" v-model="form.tipe" 
                                    :options="['DRIVER','NON DRIVER']" 
                                    :error="form.errors.tipe" placeholder="-Pilih Tipe-"/>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="mb-3">
                                    <FormSelect 
                                    label="Status Kerja" name="status_kerja" v-model="form.status_kerja" 
                                    :options="['AKTIF','NON AKTIF']" 
                                    :error="form.errors.status_kerja" placeholder="-Pilih Status Kerja-"/>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="mb-3">
                                    <FormSelect 
                                    label="Status Karyawan" name="status_karyawan" v-model="form.status_karyawan" 
                                    :options="['TETAP','KONTRAK']" 
                                    :error="form.errors.status_karyawan" placeholder="-Pilih Status Karyawan-"/>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="mb-3">
                                    <FormSelect 
                                    label="Job Class" name="job_class" v-model="form.job_class" 
                                    :options="['A1','A2','A3','B1','B2','B3','C1','C2','C3']" 
                                    :error="form.errors.job_class" placeholder="-Pilih JC-"/>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="mb-3">
                                    <FormInput label="Tanggal Efektif" name="tgl_efektif" type="date" v-model="form.tgl_efektif" :error="form.errors.tgl_efektif"/>
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
                                    <!-- <label class="form-label">Keterangan Masuk</label>
                                    <textarea v-model="form.ket_masuk" class="form-control" rows="3"></textarea> -->
                                    <FormTextarea label="Keterangan Masuk" name="ket_masuk" v-model="form.ket_masuk" :error="form.errors.ket_masuk" placeholder="Masukkan keterangan"/>
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
                        <a href="#" class="btn btn-primary btn-5 ms-auto" @click="submitForm">
                            Simpan
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- END MODAL -->

    <!-- </Layout> -->
</template>