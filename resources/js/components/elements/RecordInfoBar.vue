<template>
    <div class="operations-section ml-n2">
        <ul>
            <li>
                <span class="pl-1" v-if="isNotNull(record.os_version)">{{ record.os_version }}</span>
                <img v-bind:src="'/images/icons/' +  record.source + '.svg'">
            </li>
            <li v-if="isNotNull(record.device_model)">
                <span class="title">نوع دستگاه : </span>
                <span>{{ record.device_model }}</span>
            </li>
            <li v-if="isNotNull(record.app_version)">
                <span class="title">ورژن اپلیکیشن : </span>
                <span>{{ record.app_version }}</span>
            </li>
            <li v-if="!record.answer">
                <button v-if="Object.keys(current_record).length === 0" class="answer-btn" @click="setCurrentRecord(record)">پاسخ</button>
                <button v-if="Object.keys(current_record).length > 0" class="answer-btn-outline" @click="clearCurrentRecord()">بستن</button>
            </li>
        </ul>
    </div>
</template>

<script>
    export default {
        name: "RecordInfoBar",
        props: [
            'record'
        ],
        data() {
            return {
                'current_record': {}
            }
        },
        methods: {
            isNotNull(value)
            {
                return typeof value !==typeof undefined && value !== null && value !== 'null' && value.length > 0;
            },
            setCurrentRecord(record) {
                this.current_record = record;
                this.$emit('currentRecordSelected', record);
            },

            clearCurrentRecord() {
                this.$emit('currentRecordDeSelected');
                this.current_record = {};
            },
            answerUpdated(answer) {
                this.current_record.answer = {'answer': answer};
                this.current_record = {};
            }
        }
    }
</script>

<style scoped>

</style>
