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


const progress = ref(0)
const uploading = ref(false)

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
            { data: 'nama', name: 'nama' },
            { data: 'jabatan', name: 'jabatan' },
            { data: 'jumlah_hadir', name: 'jumlah_hadir' },
            { data: 'jumlah_terlambat', name: 'jumlah_terlambat' },
            { data: 'jumlah_pulang_cepat', name: 'jumlah_pulang_cepat' },
            {
                data: 'menit_terlambat',
                name: 'menit_terlambat',
                render: function(data) {
                    return data ?? '00:00:00'
                }
            },
            {
                data: 'menit_pulang_cepat',
                name: 'menit_pulang_cepat',
                render: function(data) {
                    return data ?? '00:00:00'
                }
            },
            { data: 'lam', name: 'lam' },
            { data: 'lap', name: 'lap' },
            { data: 'mangkir', name: 'mangkir' },
            { data: 'fraud', name: 'fraud' },
            {
                data: 'nik',
                name: 'action',
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return `
                        <button class="btn btn-info btn-sm detail-btn" data-id="${data}">
                            <i class="fa-solid fa-eye"></i>
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

const detailData = ref([])
const modalDetail = ref(null)

onMounted(() => {

  const table = getTable()

  // event klik tombol detail
  $('#setTable').on('click', '.detail-btn', function () {

      const id = $(this).data('id')

      axios.get(`/absensi/detail/${id}`)
      .then(res => {
          detailData.value = res.data

          modalDetail.value = new Modal(document.getElementById('modalDetail'))
          modalDetail.value.show()
      })

  })

})

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

const fileInput = ref(null)

const uploadFile = () => {

  if (!file.value) {
    Swal.fire('Error','Pilih file terlebih dahulu','error')
    return
  }

  const formData = new FormData()
  formData.append('file', file.value)

  uploading.value = true
  progress.value = 0

  router.post('/absensi/upload', formData, {
    forceFormData: true,

    onProgress: (event) => {
      progress.value = Math.round(event.percentage)
    },

    onSuccess: () => {
      Swal.fire('Sukses','Data berhasil diimport','success')

      getTable().ajax.reload(null, false)

      file.value = null
      fileInput.value.value = null

      uploading.value = false
      progress.value = 0
    },

    onError: () => {
      uploading.value = false
    }
  })
}

const finalisasi = () => {

  Swal.fire({
    title:'Finalisasi data?',
    text:'Data tidak bisa diubah setelah difinalisasi',
    icon:'warning',
    showCancelButton:true
  }).then((result)=>{
    if(result.isConfirmed){
      router.post('/absensi/finalisasi',{
        bulan:3,
        tahun:2026
      },{
        onSuccess:()=>{
          Swal.fire('Sukses','Data berhasil difinalisasi','success')
          getTable().ajax.reload(null,false)
        }
      })
    }
  })
}

const getBadge = (ket) => {

    if(ket === 'Libur') return 'badge bg-red text-red-fg'
    if(ket === 'Hadir') return 'badge bg-blue text-blue-fg'
    if(ket === 'Mangkir') return 'badge bg-red text-red-fg'
    if(ket === 'Lupa Absen Masuk') return 'badge bg-yellow text-yellow-fg'
    if(ket === 'Lupa Absen Pulang') return 'badge bg-yellow text-yellow-fg'
    if(ket === 'Dinas Luar Kantor Pagi') return 'badge bg-dark text-dark-fg'

    return 'bg-secondary'
}

const getRowClass = (item) => {

    if(item.keterangan === 'Mangkir'){
        return 'table-danger'
    }

    if(item.keterangan === 'Libur'){
        return 'table-secondary'
    }

    if(item.terlambat && item.terlambat !== '00:00:00'){
        return 'table-warning'
    }

    if(item.pulang_cepat && item.pulang_cepat !== '00:00:00'){
        return 'table-orange'
    }

    return ''
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
                    <div class="col-sm-6 col-lg-6">
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
                                    <input type="file" class="form-control mt-3" accept=".xlsx,.xls" @change="handleFile" ref="fileInput">
                                    <div v-if="uploading" class="mt-3">
                                        <div class="progress">
                                            <div 
                                            class="progress-bar progress-bar-striped progress-bar-animated"
                                            role="progressbar"
                                            :style="{ width: progress + '%' }"
                                            >
                                            {{ progress }}%
                                            </div>
                                        </div>
                                    </div>
                                    <!-- <button class="btn btn-primary mt-3" @click="uploadFile">Import CSV</button>
                                    <button class="btn btn-secondary mt-3">Download Format</button> -->
                                    <!-- tombol flex -->
                                    <div class="d-flex justify-content-center gap-2 mt-3">
                                        <button class="btn btn-primary" @click="uploadFile" :disabled="uploading">{{ uploading ? 'Uploading...' : 'Import Excel' }}</button>
                                        <button class="btn btn-secondary" @click="downloadFormat">Download Format</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Rank Bagian</h3>
                            </div>

                            <div class="card-body">
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-12 mt-4">
                        <div class="card">

                            <div class="card-header">
                                <h3 class="card-title">Resume Absensi</h3>
                                <button class="btn btn-danger" @click="finalisasi">
                                    Finalisasi Data
                                </button>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="setTable" class="table table-vcenter table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>NIK</th>
                                                <th>Nama</th>
                                                <th>Jabatan</th>
                                                <th>Hadir</th>
                                                <th>Terlambat</th>
                                                <th>Pulang Cepat</th>
                                                <th>Menit Terlambat</th>
                                                <th>Menit Pulang Cepat</th>
                                                <th>LAP</th>
                                                <th>LAM</th>
                                                <th>Mangkir</th>
                                                <th>Fraud</th>
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

         <div class="modal modal-blur fade" id="modalDetail" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Detail Absensi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                    <table id="setTable" class="table table-vcenter table-striped">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Shift</th>
                                <th>Normal In</th>
                                <th>Machine In</th>
                                <th>Terlambat</th>
                                <th>Normal Out</th>
                                <th>Machine Out</th>
                                <th>Pulang Cepat</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="item in detailData" :key="item.tanggal" :class="getRowClass(item)">

                                <td>{{ item.tanggal }}</td>
                                <td>{{ item.shiftcode }}</td>
                                <td>{{ item.normal_in }}</td>
                                <td>{{ item.machine_in }}</td>

                                <td>
                                <span v-if="item.terlambat != '00:00:00'" class="text-danger">
                                {{ item.terlambat }}
                                </span>
                                <span v-else>-</span>
                                </td>

                                <td>{{ item.normal_out }}</td>
                                <td>{{ item.machine_out }}</td>

                                <td>
                                <span v-if="item.pulang_cepat != '00:00:00'" class="text-danger">
                                {{ item.pulang_cepat }}
                                </span>
                                <span v-else>-</span>
                                </td>

                                <td>
                                <span class="badge" :class="getBadge(item.keterangan)">
                                    {{ item.keterangan }}
                                </span>
                                </td>

                            </tr>
                        </tbody>

                    </table>

                    </div>

                    </div>
                </div>
            </div>
        </div>
    
</template>