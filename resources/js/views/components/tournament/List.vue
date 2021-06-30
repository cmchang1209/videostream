<template>
    <div>
        <Breadcrumb :iteams="br" />
        <el-row type="flex" justify="end" style="margin-bottom: 15px">
            <el-col :span="6" style="text-align: right;">
                <el-button class="hidden-sm-and-down" type="success" round @click="addTournament">{{ $store.state.langData.cont.pageFn.tournament.addBtn }}</el-button>
                <el-button class="hidden-md-and-up" type="success" icon="el-icon-plus" circle @click="addTournament"></el-button>
            </el-col>
        </el-row>
        <br>
        <el-table :data="tableData" border :empty-text="$store.state.langData.cont.msg.data.d0001" style="width: 100%">
            <el-table-column type="index" fixed >
            </el-table-column>
            <!-- <el-table-column prop="id" :label="$store.state.langData.cont.pageFn.table.Id">
            </el-table-column> -->
            <el-table-column prop="name" fixed :label="$store.state.langData.cont.pageFn.table.Name">
            </el-table-column>
            <el-table-column prop="count" :label="$store.state.langData.cont.pageFn.table.Count">
            </el-table-column>
            <el-table-column prop="createTime" :label="$store.state.langData.cont.pageFn.table.createTime">
            </el-table-column>
            <el-table-column :label="$store.state.langData.cont.pageFn.table.Operating" width="100">
                <template slot-scope="scope">
                    <p style="margin: 0;">
                        <el-button @click.native.prevent="handleEdit(scope.$index, scope.row)" type="text">
                            {{ $store.state.langData.cont.pageFn.table.Edit }}
                        </el-button>
                    </p>
                    <p style="margin: 0;">
                        <el-button @click.native.prevent="handleDelete(scope.$index, scope.row)" type="text">
                            {{ $store.state.langData.cont.pageFn.table.Delete }}
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
    props: [],
    data() {
        return {
            br: [{
                title: this.$store.state.langData.cont.slideMenu.Tournament,
                isUrl: false
            }, {
                title: this.$store.state.langData.cont.slideMenu.List,
                isUrl: false
            }],
            tableData: []
        }
    },
    created() {
        this.changeAppLoadingStatus(true)
        this.fetchData()
    },
    methods: {
        ...mapActions(['changeAppLoadingStatus']),
        addTournament() {
            this.$router.push({ name: 'AddTournament' })
        },
        fetchData() {
            axios
                .get('/api/getTournamentListData')
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
        handleEdit(index, row) {
            this.$router.push({ name: 'EditTournament', query: { id: row.id } })
        },
        handleDelete(index, row) {
            this.changeAppLoadingStatus(true)
            axios
                .delete('/api/deleteTournament', { params: { id: row.id } })
                .then(response => {
                    let data = response.data.data
                    if (data.errorCode === 'er0000') {
                        this.$message({
                            message: this.$store.state.langData.cont.msg.database.ok0003,
                            type: 'success',
                            offset: 90
                        })
                        this.fetchData()
                        this.changeAppLoadingStatus(false)
                    } else {
                        this.$message({
                            message: this.$store.state.langData.cont.msg.database[data.errorCode],
                            type: 'error',
                            offset: 90
                        })
                        this.changeAppLoadingStatus(false)
                    }
                }).catch(error => {
                    this.changeAppLoadingStatus(false)
                    this.$message({
                        message: JSON.stringify(error),
                        type: 'error',
                        offset: 90
                    })
                })
        }
    }
}

</script>
