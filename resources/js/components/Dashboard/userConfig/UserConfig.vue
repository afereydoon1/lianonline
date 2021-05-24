<template>
    <div class="msh-box">
        <div class="msh-box-header">
            <div class="msh-box-header-icon">
                <i class="fas fa-user-cog"></i>
            </div>
            <div class="msh-box-header-text">
                تنظیمات کاربری
            </div>
        </div>
        <div class="msh-box-body">
            <tabs>
                <tab name="profile" title="اطلاعات کاربری" default_active="true">
                    <profile></profile>
                </tab>
                <tab name="password" title="تغییر رمزعبور">
                    <password></password>
                </tab>
            </tabs>
        </div>
    </div>
</template>

<script>
    import Tabs from "../../elements/Tabs/Tabs";
    import Tab from "../../elements/Tabs/Tab";
    import Profile from "./elements/Profile";
    import Password from "./elements/Password";

    export default {
        name: "UserConfig",
        components: {Password, Profile, Tab, Tabs},
        data() {
            return {

            }
        }
    }
</script>

<style scoped>

</style>
