<template>
    <div class="feedback-answer-form" @keydown="errors.clear($event.target.name)">
        <span class="error-text" v-if="errors.has('answer')">{{ errors.get('answer')}}</span>
        <textarea name="answer" class="feedback-answer-input" v-model="answer_text" placeholder="پاسخ خود را در این قسمت بنویسید"></textarea>
        <button @click="showAnswerModal" class="answer-btn feedback-answer-btn">پاسخ</button>

        <div class="msh-modal-parent" v-if="is_answering">
            <div class="msh-modal">
                <div class="msh-modal-body">
                    <div class="container mb-2" v-if="!answered">
                        <p v-if="errors.has('general')" class="alert alert-danger mb-4">{{errors.get('general')}}</p>
                        <p class="font-weight-bold mb-2 pb-2 d-block">
                            آیا از ارسال این پیام مطمئن هستید؟
                        </p>
                        <div class="custom-control custom-checkbox mt-2">
                            <input type="checkbox" disabled="disabled" class="custom-control-input" true-value="1" v-bind:id="'push_' + record.id">
                            <label class="custom-control-label" v-bind:for="'push_' + record.id"></label>
                            <label class="check-box-small-text" v-bind:for="'push_' + record.id">ارسال پوش نوتیفیکیشن</label>
                        </div>
                    </div>
                    <div class="alert alert-success" v-if="answered">
                        {{wsMessage}}
                    </div>
                    <div class="container mt-5 text-center">
                        <button :disabled="in_progress" v-if="!answered" class="answer-btn ml-2" @click="reply">
                            <img class="loading-btn" v-if="in_progress" src="images/loading.svg">
                            بله ارسال شود
                        </button>
                        <button v-if="answered" class="answer-btn ml-2" @click="closeAnswerModal">تایید</button>
                        <button v-if="!answered" @click="closeAnswerModal" class="cancel-btn">انصراف</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from "axios";
    import Errors from "../../includes/Errors";

    export default {
        name: "AnswerForm",
        props: [
            'record',
            'type'
        ],
        data() {
            return {
                'answer_text': '',
                'is_push_active': false,
                'is_answering': false,
                'answered': false,
                'wsMessage': '',
                'wsErrorMessage': '',
                'in_progress': false,
                'errors': new Errors()
            }
        },
        methods: {
            showAnswerModal() {
                if (this.answer_text.length < 3)
                {
                    this.errors.record({'answer': ['پیام خود را وارد کنید']});
                    return;
                }

                this.is_answering = true;
            },

            closeAnswerModal() {
                this.is_push_active = false;
                this.is_answering = false;
                this.in_progress = false;
                this.wsErrorMessage = '';
                this.wsMessage = '';
                if (this.answered)
                {
                    this.updateRecordAnswer();
                }
            },

            reply() {
                this.in_progress = true;
                axios.post('/panel/api/' + this.type + '/reply', {
                    'record_id': this.record.id,
                    'answer': this.answer_text,
                    'push': this.is_push_active ? true : null
                }, {timeout: 5000})
                    .then(response => {
                        if (response.data.returnCode === 0)
                        {
                            this.wsMessage = response.data.message;
                            this.answered = true;
                        }
                        else
                        {
                            if (Object.keys(data.errors).length)
                            {
                                this.errors.record(data.errors);
                            }
                            else
                            {
                                this.wsErrorMessage = response.data.message;
                            }
                        }
                        this.in_progress = false;
                    })
                    .catch(error => {
                        this.errors.handle(this, error);
                    });
            },
            updateRecordAnswer() {
                this.answered = false;
                this.$emit('answered', this.answer_text)
            }
        }
    }
</script>

<style scoped>

</style>
