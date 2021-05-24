import Dashboard from "../components/Dashboard/Dashboard";
import Login from "../components/Auth/LoginForm";
import ListCategory from "../components/Dashboard/Categories/ListCategory";
import CreateCategory from "../components/Dashboard/Categories/CreateCategory";
import EditCategory from "../components/Dashboard/Categories/EditCategory";
import CreateProduct from "../components/Dashboard/Products/CreateProduct";
import ListProduct from "../components/Dashboard/Products/ListProduct";
import EditProduct from "../components/Dashboard/Products/EditProduct";
import CreatePost from "../components/Dashboard/Posts/CreatePost";
import ListPost from "../components/Dashboard/Posts/ListPost";
import EditPost from "../components/Dashboard/Posts/EditPost";
import ListTags from "../components/Dashboard/Tags/ListTags";
import CreateTag from "../components/Dashboard/Tags/CreateTag";
import EditTag from "../components/Dashboard/Tags/EditTag";
import ListAssortment from "../components/Dashboard/Assortments/ListAssortment";
import CreateAssortment from "../components/Dashboard/Assortments/CreateAssortment";
import EditAssortment from "../components/Dashboard/Assortments/EditAssortment";
import ListUsers from "../components/Dashboard/Users/ListUsers";
import ListAdmins from "../components/Dashboard/Users/ListAdmins";
import CreateUser from "../components/Dashboard/Users/CreateUser";
import EditUser from "../components/Dashboard/Users/EditUser";
import EditAdmin from "../components/Dashboard/Users/EditAdmin";
import ListProductComments from "../components/Dashboard/Products/ListProductComments";
import ListPostComments from "../components/Dashboard/Posts/ListPostComments";
import EditComment from "../components/Dashboard/Posts/EditComment";
import General from "../components/Dashboard/Settings/General";
import Sms from "../components/Dashboard/Settings/Sms";
import Gateway from "../components/Dashboard/Settings/Gateway";
import ListOrders from "../components/Dashboard/Orders/ListOrders";
import ShowOrder from "../components/Dashboard/Orders/ShowOrder";
import ListFailedOrders from "../components/Dashboard/Orders/ListFailedOrders";

import ListCheckOut from "../components/Dashboard/Finance/ListCheckOut";
import EditCheckOut from "../components/Dashboard/Finance/EditCheckOut";
import WalletCharge from "../components/Dashboard/Finance/WalletCharge";
import ListTransactions from "../components/Dashboard/Finance/ListTransactions";
import ReferralList from "../components/Dashboard/Finance/ReferralList";
import TicketList from "../components/Dashboard/Tickets/TicketList";
import CreateTicket from "../components/Dashboard/Tickets/CreateTicket";
import EditTicket from "../components/Dashboard/Tickets/EditTicket";
import Logout from "../components/Auth/Logout";
import Slider from '../components/Dashboard/Settings/Slider'

import CopyRightList from '../components/Dashboard/Products/CopyRightList'
import ReportAbuseList from '../components/Dashboard/Products/ReportAbuseList'
import NotificationList from '../components/Dashboard/NotificationList'

let getRoutes = function(){
    let main_title = 'پنل مدیریت لیان';
    return [
        {
            path: '/management/',
            name: 'dashboard',
            component: Dashboard,
            meta: {
                title: main_title + ' - داشبورد',
            }
        },
        {
            path: '/management/login',
            name: 'login',
            component: Login,
            meta: {
                title: main_title + ' - ورود',
            }
        },
        {
            path: '/management/categories/list',
            name: 'category.list',
            component: ListCategory,
            meta: {
                title: main_title + ' - دسته بندی محصولات',
            }
        },
        {
            path: '/management/categories/create',
            name: 'category.create',
            component: CreateCategory,
            meta: {
                title: main_title + ' - افزودن دسته بندی جدید',
            }
        },
        {
            path: '/management/categories/edit/:id',
            name: 'category.edit',
            component: EditCategory,
            meta: {
                title: main_title + ' - ویرایش دسته بندی',
            }
        },
        {
            path: '/management/products/list',
            name: 'product.list',
            component: ListProduct,
            meta: {
                title: main_title + ' - مدیریت محصولات',
            }
        },
        {
            path: '/management/products/create',
            name: 'product.create',
            component: CreateProduct,
            meta: {
                title: main_title + ' - افزودن محصول جدید',
            }
        },
        {
            path: '/management/product/edit/:id',
            name: 'product.edit',
            component: EditProduct,
            meta: {
                title: main_title + ' - ویرایش محصول',
            }
        },
        {
            path: '/management/product/comments',
            name: 'product.comments',
            component: ListProductComments,
            meta: {
                title: main_title + ' - دیدگاه های محصول',
            }
        },
        {
            path: '/management/post/comments',
            name: 'posts.comments',
            component: ListPostComments,
            meta: {
                title: main_title + ' - دیدگاه های نوشته ها',
            }
        },
        {
            path: '/management/comments/edit/:id',
            name: 'comment.edit',
            component: EditComment,
            meta: {
                title: main_title + ' - ویرایش دیدگاه',
            }
        },
        {
            path: '/management/assortments/list',
            name: 'assortment.list',
            component: ListAssortment,
            meta: {
                title: main_title + ' - دسته بندی نوشته ها',
            }
        },
        {
            path: '/management/assortments/create',
            name: 'assortment.create',
            component: CreateAssortment,
            meta: {
                title: main_title + ' - افزودن دسته بندی جدید',
            }
        },
        {
            path: '/management/assortments/edit/:id',
            name: 'assortment.edit',
            component: EditAssortment,
            meta: {
                title: main_title + ' - ویرایش دسته بندی',
            }
        },
        {
            path: '/management/posts/list',
            name: 'posts.list',
            component: ListPost,
            meta: {
                title: main_title + ' - مدیریت نوشته ها',
            }
        },
        {
            path: '/management/posts/create',
            name: 'posts.create',
            component: CreatePost,
            meta: {
                title: main_title + ' - افزودن نوشته ی جدید',
            }
        },
        {
            path: '/management/posts/edit/:id',
            name: 'posts.edit',
            component: EditPost,
            meta: {
                title: main_title + ' - ویرایش نوشته',
            }
        },
        {
            path: '/management/tags/list',
            name: 'tags.list',
            component: ListTags,
            meta: {
                title: main_title + ' - مدیریت برچسب ها',
            }
        },
        {
            path: '/management/tags/create',
            name: 'tags.create',
            component: CreateTag,
            meta: {
                title: main_title + ' - افزودن برچسب جدید',
            }
        },
        {
            path: '/management/tags/edit/:id',
            name: 'tags.edit',
            component: EditTag,
            meta: {
                title: main_title + ' - ویرایش برچسب',
            }
        },
        {
            path: '/management/users/list',
            name: 'users.list',
            component: ListUsers,
            meta: {
                title: main_title + ' - مدیریت کاربران',
            }
        },
        {
            path: '/management/admins/list',
            name: 'admins.list',
            component: ListAdmins,
            meta: {
                title: main_title + ' - مدیریت ادمین ها',
            }
        },
        {
            path: '/management/users/create',
            name: 'users.create',
            component: CreateUser,
            meta: {
                title: main_title + ' - افزودن کاربر جدید',
            }
        },
        {
            path: '/management/users/edit/:id',
            name: 'users.edit',
            component: EditUser,
            meta: {
                title: main_title + ' - ویرایش کاربر',
            }
        },
        {
            path: '/management/admins/edit/:id',
            name: 'admins.edit',
            component: EditAdmin,
            meta: {
                title: main_title + ' - ویرایش ادمین',
            }
        },
        {
            path: '/management/settings/general',
            name: 'settings.general',
            component: General,
            meta: {
                title: main_title + ' - تنظیمات عمومی',
            }
        },
        {
            path: '/management/settings/sms',
            name: 'settings.sms',
            component: Sms,
            meta: {
                title: main_title + ' - تنظیمات پیامک',
            }
        },
        {
            path: '/management/settings/gateway',
            name: 'settings.gateway',
            component: Gateway,
            meta: {
                title: main_title + ' - تنظیمات بانک',
            }
        },
        {
            path: '/management/settings/slider',
            name: 'settings.slider',
            component: Slider,
            meta: {
                title: main_title + ' - تنظیمات اسلایدر',
            }
        },
        {
            path: '/management/orders/list',
            name: 'orders.list',
            component: ListOrders,
            meta: {
                title: main_title + ' - لیست سفارشات',
            }
        },
        {
            path: '/management/orders/list/failed',
            name: 'orders.failed',
            component: ListFailedOrders,
            meta: {
                title: main_title + ' - لیست سفارشات ناموفق',
            }
        },
        {
            path: '/management/orders/show/:id',
            name: 'orders.show',
            component: ShowOrder,
            meta: {
                title: main_title + ' - لیست سفارشات',
            }
        },
        {
            path: '/management/checkout/list',
            name: 'finance.checkout',
            component: ListCheckOut,
            meta: {
                title: main_title + ' - لیست تسویه حسابها',
            }
        },
        {
            path: '/management/checkout/edit/:id',
            name: 'finance.checkout.edit',
            component: EditCheckOut,
            meta: {
                title: main_title + ' - تسویه حساب',
            }
        },
        {
            path: '/management/wallet/charge',
            name: 'finance.wallet.charge',
            component: WalletCharge,
            meta: {
                title: main_title + ' - شارژ کیف پول',
            }
        },
        {
            path: '/management/transactions/list',
            name: 'finance.transactions',
            component: ListTransactions,
            meta: {
                title: main_title + ' - لیست تراکنش ها',
            }
        },
        {
            path: '/management/referral/list',
            name: 'partnership.link-sales.list',
            component: ReferralList,
            meta: {
                title: main_title + ' - گزارش بازاریابی',
            }
        },
        {
            path: '/management/tickets/list',
            name: 'tickets.list',
            component: TicketList,
            meta: {
                title: main_title + ' - لیست تیکت ها',
            }
        },
        {
            path: '/management/tickets/create',
            name: 'tickets.create',
            component: CreateTicket,
            meta: {
                title: main_title + ' - تیکت جدید',
            }
        },
        {
            path: '/management/tickets/edit/:id',
            name: 'tickets.edit',
            component: EditTicket,
            meta: {
                title: main_title + ' - تیکت',
            }
        },
        {
            path: '/management/logout',
            name: 'logout',
            component: Logout,
            meta: {
                title: main_title + ' - خروج',
            }
        },
        {
            path: '/management/product/copyright',
            name: 'copy_right.List',
            component: CopyRightList,
            meta: {
                title: main_title + ' - لیست نقض کپی رایت',
            }
        },
        {
            path: '/management/product/reportabuse',
            name: 'report_abuse.list',
            component: ReportAbuseList,
            meta: {
                title: main_title + ' - لیست گزارش تخلف',
            }
        },
        {
            path: '/management/notifications',
            name: 'notifications.list',
            component: NotificationList,
            meta: {
                title: main_title + ' - لیست اطلاعیه ها',
            }
        },
    ]
};

export default {
    mode: 'history',

    routes: getRoutes()
};

