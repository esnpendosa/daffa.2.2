from lib import *
r=random.Random(21)
defs='''
<linearGradient id="sky" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#3b97d9"/><stop offset=".5" stop-color="#9fd6f0"/><stop offset="1" stop-color="#e6f5e0"/></linearGradient>
<radialGradient id="sunG"><stop offset="0" stop-color="#fffbe0"/><stop offset=".3" stop-color="#fff0b0" stop-opacity=".6"/><stop offset="1" stop-color="#fff0b0" stop-opacity="0"/></radialGradient>
<linearGradient id="grass" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#8fd05a"/><stop offset="1" stop-color="#3f9a3a"/></linearGradient>
<linearGradient id="barn" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#d9482f"/><stop offset="1" stop-color="#a02a1c"/></linearGradient>
<linearGradient id="ray" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#fff8cc" stop-opacity=".5"/><stop offset="1" stop-color="#fff8cc" stop-opacity="0"/></linearGradient>
'''
b=f'<rect width="{W}" height="{H}" fill="url(#sky)"/>'
b+='<circle cx="1330" cy="230" r="360" fill="url(#sunG)" class="glow"/><circle cx="1330" cy="230" r="52" fill="#fffef0"/>'
for a in (-50,-34,-18,-3,12):
    b+=f'<polygon points="1330,230 {1330+math.tan(math.radians(a-3))*900:.0f},1200 {1330+math.tan(math.radians(a+3))*900:.0f},1200" fill="url(#ray)" opacity=".4"/>'
b+=cloud(260,150,1.6,.95,100,10,130)+cloud(800,100,1.2,.9,120,40,-100)+cloud(1000,290,.9,.8,90,0,90)+cloud(120,330,.9,.75,110,30,-80)
b+=birds([(600,210,.9),(650,190,.8),(700,220,.7)])
b+=hills(3,480,40,"#9ccf9a",8,1.1,.9)+hills(9,520,34,"#6fbf62",9,1.1)
b+=hills(14,570,26,"#58b04e",9,1.1)
# distant trees line
for i in range(26):
    x=r.uniform(0,W); y=575+r.uniform(-6,10); s=r.uniform(.5,.9)
    b+=f'<ellipse cx="{x:.0f}" cy="{y:.0f}" rx="{20*s:.0f}" ry="{24*s:.0f}" fill="#2f8a3c" opacity=".9"/>'
# barn
bx,by=1040,640
barn=f'<rect x="{bx}" y="{by-170}" width="270" height="170" fill="url(#barn)"/>'
barn+=f'<path d="M{bx-18},{by-168} L{bx+135},{by-262} L{bx+288},{by-168} L{bx+270},{by-160} L{bx+135},{by-242} L{bx},{by-160}Z" fill="#5b3a2a"/>'
barn+=f'<path d="M{bx+60},{by} v-96 q75,-34 150,0 v96z" fill="#f5efe2"/><path d="M{bx+60},{by-96} L{bx+210},{by}M{bx+210},{by-96} L{bx+60},{by}" stroke="#d9482f" stroke-width="8"/>'
barn+=f'<rect x="{bx+105}" y="{by-232}" width="60" height="34" fill="#fff" opacity=".9" rx="4"/><rect x="{bx+118}" y="{by-224}" width="34" height="18" fill="#6a3a22"/>'
barn+=f'<rect x="{bx+215}" y="{by-310}" width="38" height="120" fill="#cfd6d8"/><ellipse cx="{bx+234}" cy="{by-312}" rx="26" ry="9" fill="#e6ecee"/>'
barn+=f'<rect x="{bx+236}" y="{by-230}" width="26" height="46" fill="#fff" opacity=".25"/>'
b+=f'<ellipse cx="{bx+140}" cy="{by+6}" rx="200" ry="16" fill="#0b3d16" opacity=".3"/>'+barn
# main meadow
b+=f'<path d="{smooth(ridge(5,640,12,8,rough=.6))}" fill="url(#grass)"/>'
# grass stripes
for k in range(7):
    yy=690+k*34
    b+=f'<path d="{smooth([(x,yy+8*math.sin(x/190+k)) for x in range(-40,W+80,160)],False)}" stroke="#b5e57a" stroke-width="{2+k*.7:.1f}" fill="none" opacity=".4"/>'
# flowers
for i in range(90):
    x=r.uniform(0,W); y=r.uniform(680,890); s=.7+(y-680)/220
    c=r.choice(["#fff","#ffe14a","#ff9ac2"])
    b+=f'<circle cx="{x:.0f}" cy="{y:.0f}" r="{3*s:.1f}" fill="{c}" opacity=".92"/>'
# fence
fy=650
b+=f'<path d="M-20,{fy-18} Q400,{fy-30} 820,{fy-14}" stroke="#a9784a" stroke-width="7" fill="none"/><path d="M-20,{fy+10} Q400,{fy-2} 820,{fy+14}" stroke="#a9784a" stroke-width="7" fill="none"/>'
for x in range(0,860,70):
    yy=fy-18+14*(x/820)
    b+=f'<rect x="{x}" y="{yy-18:.0f}" width="12" height="70" fill="#8d5f36" rx="3"/>'
# cow
def leg(lx,cls):
    return f'<g class="{cls}"><rect x="{lx-7}" y="20" width="14" height="46" rx="5" fill="#f4f1ea"/><rect x="{lx-7}" y="58" width="14" height="9" rx="3" fill="#3a2a22"/></g>'
def cow(x,y,s,flip=1,dist=240,dur=28,delay=0,nd=0):
    g=f'<g transform="translate({x},{y}) scale({s*flip},{s})"><g class="walk" style="--d:{dist}px;--wd:{dur}s;--wl:-{delay}s">'
    g+='<ellipse cx="0" cy="62" rx="105" ry="12" fill="#000" opacity=".22"/><g class="step" style="animation-delay:-%ss">'%(delay%1)
    g+=leg(-62,'legA')+leg(-36,'legB')+leg(36,'legA')+leg(62,'legB')
    g+='<rect x="-90" y="-62" width="180" height="92" rx="40" fill="#fbf8f1"/>'
    g+='<path d="M-60,-58 q20,-10 36,6 q-8,26 -34,24z M10,-62 q30,-8 44,10 q-6,24 -40,18z M40,-8 q22,-4 30,14 q-18,10 -36,0z" fill="#2c2420"/>'
    g+='<g class="tail"><path d="M-90,-30 q-20,12 -18,50" stroke="#2c2420" stroke-width="6" fill="none" stroke-linecap="round"/><ellipse cx="-108" cy="24" rx="7" ry="12" fill="#2c2420"/></g>'
    g+='<ellipse cx="-30" cy="26" rx="22" ry="12" fill="#f6b9b0"/>'
    g+=f'<g class="nod" style="transform-origin:72px -48px;animation-delay:-{nd}s"><rect x="68" y="-84" width="62" height="64" rx="26" fill="#fbf8f1"/><ellipse cx="116" cy="-34" rx="26" ry="19" fill="#f6b9b0"/><circle cx="108" cy="-36" r="3.4" fill="#7a3a34"/><circle cx="124" cy="-36" r="3.4" fill="#7a3a34"/>'
    g+='<circle cx="95" cy="-62" r="4.4" fill="#1d1a18"/><ellipse cx="84" cy="-80" rx="12" ry="7" fill="#2c2420" transform="rotate(-30 84 -80)"/><path d="M92,-90 l-4,-18 M112,-90 l6,-18" stroke="#e8dcc0" stroke-width="5" stroke-linecap="round"/></g>'
    return g+'</g></g></g>'
b+=cow(780,676,.62,1,70,22,2,3)
# hay bales
for (x,y,s) in [(900,725,1),(960,745,.9)]:
    b+=f'<g transform="translate({x},{y}) scale({s})"><ellipse cx="0" cy="26" rx="52" ry="9" fill="#000" opacity=".2"/><circle r="42" fill="#e6c25a"/><circle r="30" fill="none" stroke="#c99a30" stroke-width="4"/><circle r="16" fill="none" stroke="#c99a30" stroke-width="4"/><circle r="4" fill="#c99a30"/></g>'
b+=cow(250,700,.9,-1,200,26,11,2)+cow(470,760,1.25,1,120,26,4,0)
# chickens
def hen(x,y,s,c="#d98a3a",dist=120,dur=16,delay=0):
    g=f'<g transform="translate({x},{y}) scale({s})"><g class="walk" style="--d:{dist}px;--wd:{dur}s;--wl:-{delay}s">'
    g+='<ellipse cx="0" cy="24" rx="26" ry="5" fill="#000" opacity=".2"/>'
    g+='<g class="legA" style="animation-duration:.35s"><path d="M-4,18 v10" stroke="#f5a623" stroke-width="3"/></g><g class="legB" style="animation-duration:.35s"><path d="M6,18 v10" stroke="#f5a623" stroke-width="3"/></g>'
    g+=f'<g class="peck" style="animation-delay:-{delay/2:.1f}s"><ellipse cx="0" cy="0" rx="24" ry="19" fill="{c}"/><path d="M-22,-4 q-14,-16 -4,-26 q8,12 10,18z" fill="#b8642a"/><circle cx="20" cy="-16" r="11" fill="{c}"/><path d="M14,-26 q4,-10 8,-2 q4,-8 8,2z" fill="#e0242a"/><path d="M30,-16 l10,3 -10,4z" fill="#f5a623"/><circle cx="23" cy="-18" r="2" fill="#222"/></g>'
    return g+'</g></g>'
b+=hen(1130,800,1.6,"#d98a3a",110,15,3)+hen(1250,840,1.3,"#f1efe6",100,13,8)+hen(1010,850,1.1,"#d98a3a",120,17,1)
b+=sparkles(8,22,100,1500,400,700,"#fff8c8",3)
open('/home/claude/agronusa-web/assets/images/banner-peternakan.svg','w').write(wrap(defs,b))
