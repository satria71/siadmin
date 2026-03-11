<script setup>
import Layout from '../../Layout.vue'
import { useDataTable } from '@/Composables/useDataTable'
import { Head } from '@inertiajs/vue3'
import { usePage } from '@inertiajs/vue3'
import { Modal } from 'bootstrap'
import Breadcrumb from '@/Components/Breadcrumb.vue'
import FormInput from '@/Components/FormInput.vue'
import FormSelect from '@/Components/FormSelect.vue'
import FormTextarea from '@/Components/FormTextarea.vue'
import Swal from 'sweetalert2'
import { ref, computed, watch, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import axios from 'axios'

const selectedIndex = ref(-1)
const loadingSearch = ref(false)
let searchTimeout = null

const suggestions = ref([])
const showSuggestions = ref(false)

const isEdit = ref(false)
const selectedId = ref(null)

const breadcrumbs = [
  { label: 'Dashboard', url: '/panel' },
  { label: 'Data Fraud', url: 'fraud' },
]

const showModal = () => {
    const modal = new Modal(document.getElementById('modal-report'))
    modal.show()
}

const user = computed(() => usePage().props.auth.user)

//reset
const form = useForm({
    nik: '',
    nama: '',
    bagian: '',
    tanggal: '',
    fraud: '',
    file_pdf: null
})

watch(() => form.nik, (nik) => {
    searchKaryawan()
})

const pilihKaryawan = (item) => {

    form.nik = item.nik
    form.nama = item.nama
    form.bagian = item.bagian

    showSuggestions.value = false
}

const searchKaryawan = () => {

    clearTimeout(searchTimeout)

    searchTimeout = setTimeout(async () => {

        if (!form.nik || form.nik.length < 2) {
            suggestions.value = []
            showSuggestions.value = false
            return
        }

        loadingSearch.value = true

        try {

            const response = await axios.get('/fraud/search', {
                params: { q: form.nik }
            })

            suggestions.value = response.data
            showSuggestions.value = true
            selectedIndex.value = -1

        } finally {
            loadingSearch.value = false
        }

    }, 300)

}

const handleKeydown = (e) => {

    if (!showSuggestions.value) return

    if (e.key === "ArrowDown") {
        e.preventDefault()
        selectedIndex.value++
        if (selectedIndex.value >= suggestions.value.length) {
            selectedIndex.value = 0
        }
    }

    if (e.key === "ArrowUp") {
        e.preventDefault()
        selectedIndex.value--
        if (selectedIndex.value < 0) {
            selectedIndex.value = suggestions.value.length - 1
        }
    }

    if (e.key === "Enter") {
        e.preventDefault()
        if (selectedIndex.value >= 0) {
            pilihKaryawan(suggestions.value[selectedIndex.value])
        }
    }

}


//error hilang saat user mengetik
const clearErrorOnInput = (field) => {
    watch(() => form[field], () => {
        form.clearErrors(field)
    })
}

clearErrorOnInput('nik')
clearErrorOnInput('tanggal')
clearErrorOnInput('fraud')

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

const { getTable } = useDataTable(
    'setTable',
    '/fraud/data',

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
            { data: 'tanggal', name: 'tanggal' ,
                render: function (data, type) {
                    if (type === 'display') {
                        const date = new Date(data);
                        return date.toLocaleDateString('id-ID', {
                            day: '2-digit',
                            month: 'long',
                            year: 'numeric'
                        });
                    }
                    return data; // untuk sorting tetap pakai format asli
                }
            },
            { data: 'nik', name: 'nik' },
            { data: 'nama', name: 'nama' },
            { data: 'fraud', name: 'fraud' },
            {
                data: 'file_exists',
                name: 'file_exists',
                render: function(data, type, row) {

                    if (data) {
                        return `
                            <span class="badge bg-green text-green-fg">ADA</span>
                        `
                    } else {
                        return `
                            <span class="badge bg-red text-red-fg">TIDAK ADA</span>
                        `
                    }

                }
            },
            {
                data: 'id',
                name: 'action',
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    let pdfButton = ''

                    if (row.file_exists) {
                        const url = `/storage/fraud/${row.file_pdf}`

                        pdfButton = `
                            <a href="${url}" target="_blank" class="btn btn-sm btn-info">
                                <i class="fa-solid fa-eye"></i>
                            </a>

                            <a href="${url}" download class="btn btn-sm btn-success">
                                <i class="fa-solid fa-download"></i>
                            </a>
                        `
                    } else {
                        pdfButton = `
                            <button class="btn btn-sm btn-secondary pdf-missing">
                                <i class="fa-solid fa-file-pdf"></i>
                            </button>
                        `
                    }

                    return `
                        <button class="btn btn-warning btn-sm edit-btn" data-id="${data}">
                            <i class="fa-solid fa-pen-to-square"> </i>
                        </button>
                        <button class="btn btn-danger btn-sm delete-btn" data-id="${data}">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                        ${pdfButton}
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

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.autocomplete-wrapper')) {
            showSuggestions.value = false
        }
    })

    // DETAIL BUTTON
    $('#setTable tbody').on('click', '.detail-btn', function () {

        const id = $(this).data('id')

        if (!id) return

        router.visit(`/fraud/${id}`)
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

                router.delete(`/fraud/delete/${id}`, {
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

    $('#setTable tbody').on('click', '.pdf-missing', function () {

        Swal.fire({
            icon: 'warning',
            title: 'File tidak tersedia',
            text: 'File PDF belum diupload'
        })

    })
})

const submitForm = () => {

    const url = isEdit.value
        ? `/fraud/update/${selectedId.value}`
        : '/fraud/store'

    const method = isEdit.value ? 'put' : 'post'

    form[method](url, {
        forceFormData: true,
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

defineProps({
    auth: Object,
    errors: Object
})

</script>

<template>
    <!-- <Layout> -->
        <Head title="Fraud" />
        <!-- BEGIN PAGE HEADER -->
        <div class="page-header d-print-none" aria-label="Page header">
            <div class="container-fluid">
                <div class="row g-2 align-items-center">
                    <div class="col">
                    <!-- Page pre-title -->
                    <!-- <div class="page-pretitle">Overview</div> -->
                    <h2 class="page-title">Fraud</h2>
                    </div>
                    <!-- Page title actions -->
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
                <div class="card">
                    <div class="card-table">
                        <div class="card-header">
                            <div class="col">
                                <h3 class="card-title mb-0">Fraud Admin</h3>
                                <p class="text-secondary m-0">Monitoring data fraud admin</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="setTable" class="table table-vcenter table-striped">
                                    <thead>
                                        <tr>
                                            <th>No. </th>
                                            <th>Tanggal</th>
                                            <th>NIK</th>
                                            <th>Nama</th>
                                            <th>Fraud</th>
                                            <th>Berita Acara</th>
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

        <!-- BEGIN MODAL -->
        <div class="modal modal-blur fade" id="modal-report" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ isEdit ? 'Edit Data Fraud' : 'Tambah Data Fraud' }}
                    </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-6 position-relative autocomplete-wrapper">
                                <FormInput label="NIK" name="nik" v-model="form.nik" :error="form.errors.nik" @keydown="handleKeydown"/>
                                <ul
                                    v-if="showSuggestions && suggestions.length"
                                    class="list-group position-absolute w-100 shadow"
                                    style="
                                        z-index:1055;
                                        background:#ffffff;
                                        border:1px solid #dee2e6;
                                        max-height:200px;
                                        overflow-y:auto;
                                    "
                                >
                                    <li v-if="loadingSearch" class="list-group-item text-muted">
                                        Searching...
                                    </li>
                                    <li
                                        v-for="item in suggestions"
                                        :key="item.nik"
                                        class="list-group-item list-group-item-action"
                                        @click="pilihKaryawan(item)"
                                    >
                                        {{ item.nik }} - {{ item.nama }} - {{ item.bagian }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-6">
                                <FormInput label="Nama" name="nama" v-model="form.nama" readonly/>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-6">
                                <FormInput label="Bagian" name="bagian" v-model="form.bagian" readonly/>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-6">
                                <FormInput label="Tanggal Fraud" name="tanggal" type="date" v-model="form.tanggal" :error="form.errors.tanggal"/>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-6">
                                <FormTextarea label="Fraud" name="fraud" v-model="form.fraud" :error="form.errors.fraud" placeholder="Masukkan kronologi fraud"/>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-label">Custom File Input</div>
                            <input 
                                type="file" 
                                class="form-control"
                                accept="application/pdf"
                                @change="e => form.file_pdf = e.target.files[0]"
                            >
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