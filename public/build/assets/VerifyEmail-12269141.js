import{T as f,k as m,o as a,c as g,w as s,_ as y,a as o,u as e,Z as h,i as _,j as p,b as i,g as r,n as k,P as x,l as v,h as b}from"./app-bcee2fb0.js";const w=i("div",{class:"mb-4 text-sm text-gray-600 dark:text-gray-400"}," Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another. ",-1),V={key:0,class:"mb-4 font-medium text-sm text-green-600 dark:text-green-400"},B=["onSubmit"],E={class:"mt-4 flex items-center justify-between"},j={__name:"VerifyEmail",props:{status:{type:String}},setup(n){const c=n,t=f({}),d=()=>{t.post(route("verification.send"))},u=m(()=>c.status==="verification-link-sent");return(l,N)=>(a(),g(y,null,{default:s(()=>[o(e(h),{title:"Email Verification"}),w,u.value?(a(),_("div",V," A new verification link has been sent to the email address you provided during registration. ")):p("",!0),i("form",{onSubmit:b(d,["prevent"])},[i("div",E,[o(x,{class:k({"opacity-25":e(t).processing}),disabled:e(t).processing},{default:s(()=>[r(" Resend Verification Email ")]),_:1},8,["class","disabled"]),o(e(v),{href:l.route("logout"),method:"post",as:"button",class:"underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"},{default:s(()=>[r("Log Out")]),_:1},8,["href"])])],40,B)]),_:1}))}};export{j as default};