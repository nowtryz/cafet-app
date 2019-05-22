import Buttons from "views/Components/Buttons.jsx";
import Calendar from "views/Calendar/Calendar.jsx";
import Charts from "views/Charts/Charts.jsx";
import Dashboard from "views/Dashboard/Dashboard.jsx";
import ErrorPage from "views/Pages/ErrorPage.jsx";
import ExtendedForms from "views/Forms/ExtendedForms.jsx";
import ExtendedTables from "views/Tables/ExtendedTables.jsx";
import FullScreenMap from "views/Maps/FullScreenMap.jsx";
import GoogleMaps from "views/Maps/GoogleMaps.jsx";
import GridSystem from "views/Components/GridSystem.jsx";
import Icons from "views/Components/Icons.jsx";
import LockScreenPage from "views/Pages/LockScreenPage.jsx";
import LoginPage from "views/Pages/LoginPage.jsx";
import Notifications from "views/Components/Notifications.jsx";
import Panels from "views/Components/Panels.jsx";
import PricingPage from "views/Pages/PricingPage.jsx";
import RTLSupport from "views/Pages/RTLSupport.jsx";
import ReactTables from "views/Tables/ReactTables.jsx";
import RegisterPage from "views/Pages/RegisterPage.jsx";
import RegularForms from "views/Forms/RegularForms.jsx";
import RegularTables from "views/Tables/RegularTables.jsx";
import SweetAlert from "views/Components/SweetAlert.jsx";
import TimelinePage from "views/Pages/Timeline.jsx";
import Typography from "views/Components/Typography.jsx";
import UserProfile from "views/Pages/UserProfile.jsx";
import ValidationForms from "views/Forms/ValidationForms.jsx";
import VectorMap from "views/Maps/VectorMap.jsx";
import Widgets from "views/Widgets/Widgets.jsx";
import Wizard from "views/Forms/Wizard.jsx";

// @material-ui/icons
import Apps from "@material-ui/icons/Apps";
import DashboardIcon from "@material-ui/icons/Dashboard";
import DateRange from "@material-ui/icons/DateRange";
import GridOn from "@material-ui/icons/GridOn";
import Image from "@material-ui/icons/Image";
import Place from "@material-ui/icons/Place";
import Timeline from "@material-ui/icons/Timeline";
import WidgetsIcon from "@material-ui/icons/Widgets";

var dashRoutes = [
  {
    path: "/dashboard",
    name: "Dashboard",
    rtlName: "لوحة القيادة",
    icon: DashboardIcon,
    component: Dashboard,
    layout: "/demo/admin"
  },
  {
    collapse: true,
    name: "Pages",
    rtlName: "صفحات",
    icon: Image,
    state: "pageCollapse",
    views: [
      {
        path: "/pricing-page",
        name: "Pricing Page",
        rtlName: "عالتسعير",
        mini: "PP",
        rtlMini: "ع",
        component: PricingPage,
        layout: "/demo/auth"
      },
      {
        path: "/rtl-support-page",
        name: "RTL Support",
        rtlName: "صودعم رتل",
        mini: "RS",
        rtlMini: "صو",
        component: RTLSupport,
        layout: "/demo/rtl"
      },
      {
        path: "/timeline-page",
        name: "Timeline Page",
        rtlName: "تيالجدول الزمني",
        mini: "T",
        rtlMini: "تي",
        component: TimelinePage,
        layout: "/demo/admin"
      },
      {
        path: "/login-page",
        name: "Login Page",
        rtlName: "هعذاتسجيل الدخول",
        mini: "L",
        rtlMini: "هعذا",
        component: LoginPage,
        layout: "/demo/auth"
      },
      {
        path: "/register-page",
        name: "Register Page",
        rtlName: "تسجيل",
        mini: "R",
        rtlMini: "صع",
        component: RegisterPage,
        layout: "/demo/auth"
      },
      {
        path: "/lock-screen-page",
        name: "Lock Screen Page",
        rtlName: "اقفل الشاشة",
        mini: "LS",
        rtlMini: "هذاع",
        component: LockScreenPage,
        layout: "/demo/auth"
      },
      {
        path: "/user-page",
        name: "User Profile",
        rtlName: "ملف تعريفي للمستخدم",
        mini: "UP",
        rtlMini: "شع",
        component: UserProfile,
        layout: "/demo/admin"
      },
      {
        path: "/error-page",
        name: "Error Page",
        rtlName: "صفحة الخطأ",
        mini: "E",
        rtlMini: "البريد",
        component: ErrorPage,
        layout: "/demo/auth"
      }
    ]
  },
  {
    collapse: true,
    name: "Components",
    rtlName: "المكونات",
    icon: Apps,
    state: "componentsCollapse",
    views: [
      {
        collapse: true,
        name: "Multi Level Collapse",
        rtlName: "انهيار متعدد المستويات",
        mini: "MC",
        rtlMini: "ر",
        state: "multiCollapse",
        views: [
          {
            path: "/buttons",
            name: "Buttons",
            rtlName: "وصفت",
            mini: "B",
            rtlMini: "ب",
            component: Buttons,
            layout: "/demo/admin"
          }
        ]
      },
      {
        path: "/buttons",
        name: "Buttons",
        rtlName: "وصفت",
        mini: "B",
        rtlMini: "ب",
        component: Buttons,
        layout: "/demo/admin"
      },
      {
        path: "/grid-system",
        name: "Grid System",
        rtlName: "نظام الشبكة",
        mini: "GS",
        rtlMini: "زو",
        component: GridSystem,
        layout: "/demo/admin"
      },
      {
        path: "/panels",
        name: "Panels",
        rtlName: "لوحات",
        mini: "P",
        rtlMini: "ع",
        component: Panels,
        layout: "/demo/admin"
      },
      {
        path: "/sweet-alert",
        name: "Sweet Alert",
        rtlName: "الحلو تنبيه",
        mini: "SA",
        rtlMini: "ومن",
        component: SweetAlert,
        layout: "/demo/admin"
      },
      {
        path: "/notifications",
        name: "Notifications",
        rtlName: "إخطارات",
        mini: "N",
        rtlMini: "ن",
        component: Notifications,
        layout: "/demo/admin"
      },
      {
        path: "/icons",
        name: "Icons",
        rtlName: "الرموز",
        mini: "I",
        rtlMini: "و",
        component: Icons,
        layout: "/demo/admin"
      },
      {
        path: "/typography",
        name: "Typography",
        rtlName: "طباعة",
        mini: "T",
        rtlMini: "ر",
        component: Typography,
        layout: "/demo/admin"
      }
    ]
  },
  {
    collapse: true,
    name: "Forms",
    rtlName: "إستمارات",
    icon: "content_paste",
    state: "formsCollapse",
    views: [
      {
        path: "/regular-forms",
        name: "Regular Forms",
        rtlName: "أشكال عادية",
        mini: "RF",
        rtlMini: "صو",
        component: RegularForms,
        layout: "/demo/admin"
      },
      {
        path: "/extended-forms",
        name: "Extended Forms",
        rtlName: "نماذج موسعة",
        mini: "EF",
        rtlMini: "هوو",
        component: ExtendedForms,
        layout: "/demo/admin"
      },
      {
        path: "/validation-forms",
        name: "Validation Forms",
        rtlName: "نماذج التحقق من الصحة",
        mini: "VF",
        rtlMini: "تو",
        component: ValidationForms,
        layout: "/demo/admin"
      },
      {
        path: "/wizard",
        name: "Wizard",
        rtlName: "ساحر",
        mini: "W",
        rtlMini: "ث",
        component: Wizard,
        layout: "/demo/admin"
      }
    ]
  },
  {
    collapse: true,
    name: "Tables",
    rtlName: "الجداول",
    icon: GridOn,
    state: "tablesCollapse",
    views: [
      {
        path: "/regular-tables",
        name: "Regular Tables",
        rtlName: "طاولات عادية",
        mini: "RT",
        rtlMini: "صر",
        component: RegularTables,
        layout: "/demo/admin"
      },
      {
        path: "/extended-tables",
        name: "Extended Tables",
        rtlName: "جداول ممتدة",
        mini: "ET",
        rtlMini: "هور",
        component: ExtendedTables,
        layout: "/demo/admin"
      },
      {
        path: "/react-tables",
        name: "React Tables",
        rtlName: "رد فعل الطاولة",
        mini: "RT",
        rtlMini: "در",
        component: ReactTables,
        layout: "/demo/admin"
      }
    ]
  },
  {
    collapse: true,
    name: "Maps",
    rtlName: "خرائط",
    icon: Place,
    state: "mapsCollapse",
    views: [
      {
        path: "/google-maps",
        name: "Google Maps",
        rtlName: "خرائط جوجل",
        mini: "GM",
        rtlMini: "زم",
        component: GoogleMaps,
        layout: "/demo/admin"
      },
      {
        path: "/full-screen-maps",
        name: "Full Screen Map",
        rtlName: "خريطة كاملة الشاشة",
        mini: "FSM",
        rtlMini: "ووم",
        component: FullScreenMap,
        layout: "/demo/admin"
      },
      {
        path: "/vector-maps",
        name: "Vector Map",
        rtlName: "خريطة المتجه",
        mini: "VM",
        rtlMini: "تم",
        component: VectorMap,
        layout: "/demo/admin"
      }
    ]
  },
  {
    path: "/widgets",
    name: "Widgets",
    rtlName: "الحاجيات",
    icon: WidgetsIcon,
    component: Widgets,
    layout: "/demo/admin"
  },
  {
    path: "/charts",
    name: "Charts",
    rtlName: "الرسوم البيانية",
    icon: Timeline,
    component: Charts,
    layout: "/demo/admin"
  },
  {
    path: "/calendar",
    name: "Calendar",
    rtlName: "التقويم",
    icon: DateRange,
    component: Calendar,
    layout: "/demo/admin"
  }
];
export default dashRoutes;
