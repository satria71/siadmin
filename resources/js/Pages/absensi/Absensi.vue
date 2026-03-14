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

const breadcrumbs = [
  { label: 'Dashboard', url: '/panel' },
  { label: 'Kelola Data', url: 'absensi' },
]

const columns = [
    { data: null, orderable:false, searchable:false },
    { data: 'nik' },
    { data: 'jumlah_terlambat' },
    { data: 'menit_terlambat' },
    { data: 'jumlah_pulang_cepat' },
    { data: 'menit_pulang_cepat' }
]

const { getTable } = useDataTable(
    'setTable',
    '/absensi/data',

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
            { data: 'jumlah_terlambat', name: 'jumlah_terlambat' },
            { data: 'menit_terlambat', name: 'menit_terlambat' },
            { data: 'jumlah_pulang_cepat', name: 'jumlah_pulang_cepat' },
            { data: 'menit_pulang_cepat', name: 'menit_pulang_cepat' },
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


const user = computed(() => usePage().props.auth.user)

const file = ref(null)

const statistik = ref({
  hadir: 0,
  terlambat: 0,
  alpha: 0
})

const handleFile = (e) => {
  file.value = e.target.files[0]
}

const uploadFile = () => {

  if (!file.value) {
    Swal.fire('Error','Pilih file terlebih dahulu','error')
    return
  }

  const formData = new FormData()
  formData.append('file', file.value)

  router.post('/absensi/upload', formData, {
    forceFormData: true,
    onSuccess: () => {
      Swal.fire('Sukses','Data berhasil diimport','success')
    }
  })
}

defineOptions({
  layout: Layout
})

defineProps({
    auth: Object,
    errors: Object,
    resume: Array
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
                    <h2 class="page-title">Absensi Admin</h2>
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
                <div class="row row-deck row-cards">
                    <div class="col-sm-6 col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Upload Absensi CSV</h3>
                            </div>

                            <div class="card-body">
                                <div class="upload-box text-center p-5 border border-dashed rounded">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                    width="80"
                                    height="80"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.5"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="text-primary mb-3">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M7 18a4.6 4.4 0 0 1 0 -9a5 4.5 0 0 1 11 2h1a3.5 3.5 0 0 1 0 7h-1"/>
                                    <path d="M9 15l3 -3l3 3"/>
                                    <path d="M12 12l0 9"/>
                                    </svg>
                                    <h3>Upload File Absensi</h3>
                                    <p class="text-muted">Drag & drop CSV atau pilih file</p>
                                    <input type="file" class="form-control mt-3" accept=".xlsx,.xls" @change="handleFile">
                                    <button class="btn btn-primary mt-3" @click="uploadFile">Import CSV</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-12 mt-4">
                        <div class="card">

                            <div class="card-header">
                                <h3 class="card-title">Resume Absensi</h3>
                            </div>

                            <div class="table-responsive">
                                <table id="setTable" class="table table-vcenter table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>NIK</th>
                                            <th>Jumlah Terlambat</th>
                                            <th>Menit Terlambat</th>
                                            <th>Pulang Cepat</th>
                                            <th>Menit Pulang Cepat</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>      
         </div>
    
</template>