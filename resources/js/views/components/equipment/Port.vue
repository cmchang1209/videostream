<template>
    <div class="equipment-port">
        <Breadcrumb :iteams="br" />
        <br>
        <el-table :data="tableData" border :empty-text="$store.state.langData.cont.msg.data.d0001" style="width: 100%">
            <el-table-column prop="usb_id" :label="$store.state.langData.cont.pageFn.table.USB">
            </el-table-column>
            <el-table-column :label="$store.state.langData.cont.pageFn.table.Type">
                <template slot-scope="scope">
                    {{ $store.state.langData.cont.pageFn.equipment.portFunction[scope.row.usb_id - 1] }}
                </template>
            </el-table-column>
            <el-table-column prop="dev_name" :label="$store.state.langData.cont.pageFn.table.Name">
            </el-table-column>
            <el-table-column prop="port_no" :label="$store.state.langData.cont.pageFn.table.Port">
            </el-table-column>
            <el-table-column fixed="right" :label="$store.state.langData.cont.pageFn.table.Operating" width="100">
                <template slot-scope="scope">
                    <p v-if="scope.row.usb_id!==3" style="margin: 0;">
                        <!-- <el-button @click.native.prevent="handleView(scope.$index, scope.row)" type="text">
                            {{ $store.state.langData.cont.pageFn.table.View }}
                        </el-button> -->
                        <a :href="viewUrl(scope.row)" target="_blank">
                        	{{ $store.state.langData.cont.pageFn.table.View }}
                        </a>
                    </p>
                    <p v-if="scope.row.usb_id!==3" style="margin: 0;">
                        <el-button @click.native.prevent="handleCopyUrl(scope.$index, scope.row)" type="text">
                            {{ $store.state.langData.cont.pageFn.table.CopyUrl }}
                        </el-button>
                    </p>
                    <p v-if="scope.row.usb_id===1" style="margin: 0;">
                        <el-button @click.native.prevent="handleAudio(scope.$index, scope.row)" type="text">
                            {{ scope.row.audio? $store.state.langData.cont.pageFn.table.Mute : $store.state.langData.cont.pageFn.table.Audio }}
                        </el-button>
                    </p>
                </template>
            </el-table-column>
        </el-table>
    </div>
</template>
<script>
import { mapActions } from 'vuex'
import Breadcrumb from '../layout/Breadcrumb.vue'
export default {
    components: { Breadcrumb },
    props: ['id'],
    data() {
        return {
            br: [{
                    hasback: true,
                    name: 'EquipmentList',
                    isUrl: false
                },
                {
                    title: this.$store.state.langData.cont.slideMenu.Equipment,
                    isUrl: false
                }, {
                    title: this.$store.state.langData.cont.pageFn.golbal.Port,
                    isUrl: false
                }
            ],
            tableData: []
        }
    },
    created() {
        this.changeAppLoadingStatus(true)
        this.fetchData()
    },
    methods: {
        ...mapActions(['changeAppLoadingStatus']),
        fetchData() {
            axios
                .get('/api/getEquipmentPort', {
                    params: { id: this.id }
                })
                .then(response => {
                    let data = response.data.data
                    if (data.errorCode === 'er0000') {
                        this.tableData = data.data
                        this.changeAppLoadingStatus(false)
                    }
                }).catch(error => {
                    this.changeAppLoadingStatus(false)
                    console.log(error)
                })
        },
        handleAudio(index, row) {
        	this.changeAppLoadingStatus(true)
            axios
                .post('/api/setAudio', {
                    audio: row.audio,
                    id: this.id
                })
                .then(response => {
                    let data = response.data.data
                    if (data.errorCode === 'er0000') {
                        this.$message({
                            message: this.$store.state.langData.cont.msg.database.ok0003,
                            type: 'success',
                            offset: 90
                        })
                        this.fetchData()
                    } else {
                        this.$message({
                            message: this.$store.state.langData.cont.msg.database[data.errorCode],
                            type: 'error',
                            offset: 90
                        })
                        this.changeAppLoadingStatus(false)
                    }
                }).catch(error => {
                    this.$message({
                        message: JSON.stringify(error),
                        type: 'error',
                        offset: 90
                    })
                    this.changeAppLoadingStatus(false)
                })
        },
        viewUrl(row) {
            //return `http://localhost:8080/work?id=${this.id}&usb=${row.usb_id}`
            return `http://videostream.fidodarts.com:8005/work?id=${this.id}&usb=${row.usb_id}`
        	//return `http://${document.location.hostname}/work?port=${port}&usb=${usb}`
        }
    }
}

</script>
<style scoped lang="scss">
a {
	text-decoration: none;
	color: #409EFF;
}
</style>
