from lib import *
r=random.Random(41)
defs='''
<linearGradient id="sky" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#1b2a6b"/><stop offset=".45" stop-color="#5b4bc4"/><stop offset=".72" stop-color="#e98ab0"/><stop offset="1" stop-color="#ffc49a"/></linearGradient>
<radialGradient id="sunG"><stop offset="0" stop-color="#fff0d0"/><stop offset=".3" stop-color="#ffc0a0" stop-opacity=".6"/><stop offset="1" stop-color="#ff90b0" stop-opacity="0"/></radialGradient>
<linearGradient id="field" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#3fb36a"/><stop offset="1" stop-color="#157a46"/></linearGradient>
<linearGradient id="spray" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#bdf3ff" stop-opacity=".85"/><stop offset="1" stop-color="#bdf3ff" stop-opacity="0"/></linearGradient>
<linearGradient id="gh" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#c8fff0" stop-opacity=".55"/><stop offset="1" stop-color="#6df0b0" stop-opacity=".25"/></linearGradient>
<radialGradient id="led"><stop offset="0" stop-color="#ff5ad8" stop-opacity=".9"/><stop offset="1" stop-color="#ff5ad8" stop-opacity="0"/></radialGradient>
'''
HZ=500
b=f'<rect width="{W}" height="{H}" fill="url(#sky)"/>'
b+=sparkles(3,60,0,1600,0,330,"#fff",2.2)
b+=f'<circle cx="1180" cy="{HZ}" r="420" fill="url(#sunG)" class="glow"/><circle cx="1180" cy="{HZ}" r="46" fill="#fff3dc"/>'
b+=cloud(300,180,1.5,.35,110,0,130)+cloud(900,120,1.2,.3,100,40,-110)
b+=hills(12,HZ+10,24,"#7a4f9a",9,1.2,.85)+hills(17,HZ+40,18,"#3f6a8f",9,1,.9)
b+=f'<path d="{smooth(ridge(3,HZ+70,10,7,rough=.5))}" fill="url(#field)"/>'
# crop rows with perspective to vanishing point
vp=(800,HZ+75)
for k in range(-14,15):
    ex=vp[0]+k*190
    b+=f'<path d="M{vp[0]},{vp[1]} L{ex},{H+40}" stroke="{"#7be08a" if k%2==0 else "#1c8a4e"}" stroke-width="{10 if k%2 else 6}" opacity=".55"/>'
for k in range(1,12):
    t=(k/12)**1.8; y=vp[1]+(H-vp[1])*t
    b+=f'<path d="M0,{y:.0f} H{W}" stroke="#0b5a34" stroke-width="{1+t*3:.1f}" opacity=".28"/>'
# solar panels row (left)
for i in range(6):
    x=40+i*70;y=HZ+60+i*8
    b+=f'<g transform="translate({x},{y}) skewX(-24)"><rect width="56" height="30" fill="#1d3f8f" stroke="#a9c8ff" stroke-width="1.5"/><path d="M19,0 v30 M38,0 v30 M0,15 h56" stroke="#a9c8ff" stroke-width="1" opacity=".7"/></g>'
# greenhouse
gx,gy=1050,HZ+150
g=f'<path d="M{gx},{gy} v-90 q170,-110 340,0 v90z" fill="url(#gh)" stroke="#e8fff6" stroke-width="3"/>'
for k in range(1,7): g+=f'<path d="M{gx+k*48.5},{gy} V{gy-90-(110*(1-((k*48.5-170)/170)**2)):.0f}" stroke="#e8fff6" stroke-width="1.6" opacity=".7"/>'
for k in range(5): g+=f'<rect x="{gx+14}" y="{gy-14-k*0}" width="312" height="0"/>'
# hydroponic rows inside
for k,yy in enumerate((gy-24,gy-52)):
    g+=f'<rect x="{gx+18}" y="{yy}" width="304" height="8" fill="#e9f7f2" opacity=".85" rx="3"/>'
    for j in range(12): g+=f'<ellipse cx="{gx+34+j*24}" cy="{yy-8}" rx="9" ry="11" fill="#6fe08a"/>'
g+=f'<ellipse cx="{gx+170}" cy="{gy-86}" rx="150" ry="40" fill="url(#led)" class="glow"/>'
b+=g
# IoT sensor poles with rings
def sensor(x,y,s=1):
    o=f'<rect x="{x-3*s}" y="{y-70*s}" width="{6*s}" height="{70*s}" fill="#dfe8f2"/><rect x="{x-12*s}" y="{y-86*s}" width="{24*s}" height="{18*s}" rx="{4*s}" fill="#ffffff"/><circle cx="{x}" cy="{y-77*s}" r="{3.2*s}" fill="#27e0a1" class="tw"/>'
    for k,rr in enumerate((26,44,64)):
        o+=f'<path d="M{x-rr*s*.8},{y-90*s-rr*s*.45} A{rr*s},{rr*s} 0 0 1 {x+rr*s*.8},{y-90*s-rr*s*.45}" stroke="#7ff3d0" stroke-width="{2.4*s}" fill="none" stroke-linecap="round" class="tw" style="animation-delay:-{k*.8}s"/>'
    return o
b+=sensor(520,760,1.1)+sensor(280,690,.8)+sensor(1380,800,1.2)+sensor(930,660,.6)
# drone + spray (terbang bolak-balik, baling-baling berputar, semprotan menetes)
def drone(x,y,s,dist,dur,delay):
    o=f'<g transform="translate({x},{y}) scale({s})"><g class="fly" style="--fd:{dist}px;--fdur:{dur}s;--fdl:-{delay}s"><g class="bob">'
    o+='<polygon points="-30,40 30,40 150,340 -150,340" fill="url(#spray)" opacity=".55"/>'
    for k in range(46):
        t=r.uniform(.1,1); cx=r.uniform(-120,120)*t
        o+=f'<circle class="fall" cx="{cx:.0f}" cy="{60+t*190:.0f}" r="{r.uniform(1.3,3.4):.1f}" fill="#fff" style="animation-delay:-{r.uniform(0,1.1):.2f}s"/>'
    o+='<ellipse cx="0" cy="360" rx="120" ry="12" fill="#0a3a28" opacity=".3"/>'
    for sx in (-1,1):
        o+=f'<path d="M0,0 L{sx*110},-22" stroke="#e9eef5" stroke-width="10" stroke-linecap="round"/><path d="M0,10 L{sx*110},22" stroke="#e9eef5" stroke-width="10" stroke-linecap="round"/>'
        for dy in (-22,22):
            o+=f'<rect x="{sx*110-6}" y="{dy-8}" width="12" height="8" fill="#2a2f3a" rx="3"/><ellipse class="prop" cx="{sx*110}" cy="{dy-10}" rx="58" ry="6" fill="#cfd8e6" opacity=".6" style="animation-delay:-{r.uniform(0,.1):.3f}s"/>'
    o+='<rect x="-44" y="-24" width="88" height="54" rx="22" fill="#ffffff"/><rect x="-30" y="-12" width="60" height="22" rx="10" fill="#1d2a44"/><circle class="tw" cx="-14" cy="-1" r="4" fill="#27e0a1"/><circle class="tw" cx="14" cy="-1" r="4" fill="#27e0a1"/>'
    o+='<rect x="-18" y="30" width="36" height="30" rx="8" fill="#2fa86a"/><path d="M-10,60 v10 h20 v-10" fill="#1d2a44"/>'
    return o+'</g></g></g>'
b+=drone(800,250,1.15,260,11,0)+drone(1380,200,.55,120,8,3)
b+=sparkles(6,30,100,1500,500,860,"#c8fff0",3)
open('/home/claude/agronusa-web/assets/images/banner-teknologi-agro.svg','w').write(wrap(defs,b))
