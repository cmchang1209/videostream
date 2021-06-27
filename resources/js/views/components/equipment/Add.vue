<template>
    <div class="equipment-add">
        <Breadcrumb :iteams="br" />
        <br>
        <el-form :model="form" :rules="rules" ref="ruleForm" label-position="top">
            <!-- <el-form-item :label="$store.state.langData.cont.pageFn.table.Name">
                <el-autocomplete v-model="player.name" clearable :placement="'top-start'" :fetch-suggestions="querySearchAsync" :placeholder="$store.state.langData.cont.msg.placeholder.ph0002" @select="handleSelect" @blur="handleBlur" @clear="handleClear" style="width: 100%"></el-autocomplete>
            </el-form-item> -->
        </el-form>
    </div>
</template>
<script>
import Breadcrumb from '../layout/Breadcrumb.vue'
export default {
    components: { Breadcrumb },
    data() {
        var validateMac = (rule, value, callback) => {
            if (value.trim() === '') {
                callback(new Error('Please enter equipment mac'))
            } else {
                var regexp = /^(([A-Fa-f0-9]{2}[:]){5}[A-Fa-f0-9]{2}[,]?)+$/i
                if (regexp.test(value)) {
                    callback()
                } else {
                    callback(new Error('Input format error'))
                }
            }
        }
        var validateName = (rule, value, callback) => {
            if (value.trim() === '') {
                callback(new Error('Please enter equipment name'))
            } else {
                callback()
            }
        }
        var validatePassword = (rule, value, callback) => {
            if (value.trim() === '') {
                callback(new Error('Please enter password'))
            } else {
                callback()
            }
        }
        var validateDistributor = (rule, value, callback) => {
            if (value.trim() === '') {
                callback(new Error('Please select equipment distributor'))
            } else {
                callback()
            }
        }
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
                    title: this.$store.state.langData.cont.pageFn.golbal.Add,
                    isUrl: false
                }
            ],
            form: {
                mac: '',
                name: '',
                password: 'fidodarts',
                distributor: '',
                store: null,
                storeId: null,
                nation: '',
                description: `Raspberry Pi 4 Model B 8G\nSD:32G\nUbuntu 20.04.2 LTS (GNU/Linux 5.4.0-1034-raspi aarch64)`
            },
            loading: false,
            rules: {
                mac: [
                    { validator: validateMac, required: true, trigger: 'no' }
                ],
                name: [
                    { validator: validateName, required: true, trigger: 'no' }
                ],
                password: [
                    { validator: validatePassword, required: true, trigger: 'no' }
                ],
                distributor: [
                    { validator: validateDistributor, required: true, trigger: 'no' }
                ]
            }
        }
    },
    created() {},
    methods: {}
}

</script>
