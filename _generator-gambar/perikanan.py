from lib import *
r=random.Random(31)
defs='''
<linearGradient id="sky" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#2d5fae"/><stop offset=".35" stop-color="#d0709a"/><stop offset=".6" stop-color="#ffa86a"/><stop offset="1" stop-color="#ffd98a"/></linearGradient>
<radialGradient id="sunG"><stop offset="0" stop-color="#fff6d0"/><stop offset=".3" stop-color="#ffd080" stop-opacity=".7"/><stop offset="1" stop-color="#ff9a50" stop-opacity="0"/></radialGradient>
<linearGradient id="sea" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#ffc07a"/><stop offset=".25" stop-color="#e58a8e"/><stop offset=".6" stop-color="#2e86b4"/><stop offset="1" stop-color="#0f4d7a"/></linearGradient>
<linearGradient id="glint" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#fff0b8" stop-opacity=".95"/><stop offset="1" stop-color="#fff0b8" stop-opacity="0"/></linearGradient>
<linearGradient id="fishg" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#7fd0f0"/><stop offset=".5" stop-color="#d9eef8"/><stop offset="1" stop-color="#f7a24a"/></linearGradient>
<linearGradient id="ray" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#ffe9a8" stop-opacity=".55"/><stop offset="1" stop-color="#ffe9a8" stop-opacity="0"/></linearGradient>
'''
HZ=520
b=f'<rect width="{W}" height="{H}" fill="url(#sky)"/>'
b+=f'<circle cx="800" cy="{HZ-10}" r="480" fill="url(#sunG)" class="glow"/>'
for a in (-70,-52,-36,-20,-6,8,22,38,54,70):
    b+=f'<polygon points="800,{HZ-10} {800+math.tan(math.radians(a-2.5))*700:.0f},-200 {800+math.tan(math.radians(a+2.5))*700:.0f},-200" fill="url(#ray)" opacity=".28"/>'
b+=f'<circle cx="800" cy="{HZ-20}" r="76" fill="#fffbe2"/>'
b+=cloud(300,180,1.5,.55,110,20,120)+cloud(1250,150,1.4,.5,100,60,-120)+cloud(1000,300,1,.45,90,0,80)
b+=birds([(500,260,1),(560,235,.8),(610,270,.8),(1050,200,.7),(1100,185,.6)],"#3a1f3a",.7)
# far island + mangrove silhouette
b+=hills(41,HZ-14,12,"#6b3a58",10,1.4,.8)
b+=f'<rect y="{HZ-30}" width="{W}" height="40" fill="url(#ray)" opacity=".0"/>'
b+=f'<rect y="{HZ-2}" width="{W}" height="{H-HZ+2}" fill="url(#sea)"/>'
# sun reflection column
b+=f'<polygon points="740,{HZ} 860,{HZ} 1040,{H} 560,{H}" fill="url(#glint)" opacity=".7" filter="url(#blur6)"/>'
# wave lines
for i in range(70):
    t=r.random()**1.6; y=HZ+10+t*(H-HZ-20); w=30+t*220; x=r.uniform(-50,W)
    near=abs(x-800)<(60+t*260)
    c="#fff3c2" if near else "#bfe3f2"
    b+=f'<path d="M{x:.0f},{y:.0f} q{w/4:.0f},{-4-t*6:.0f} {w/2:.0f},0 t{w/2:.0f},0" stroke="{c}" stroke-width="{1.2+t*2.6:.1f}" fill="none" stroke-linecap="round" opacity="{.75 if near else .35}" class="rip" style="animation-delay:-{r.uniform(0,6):.1f}s"/>'
# floating cage net (keramba) left
def keramba(x,y,s):
    g=f'<g transform="translate({x},{y}) scale({s})"><g class="bob">'
    g+='<ellipse cx="0" cy="22" rx="150" ry="16" fill="#0a2e4d" opacity=".35"/>'
    g+='<path d="M-140,0 L140,0 L120,24 L-120,24Z" fill="#8a5a30"/><path d="M-140,0 L140,0" stroke="#c99560" stroke-width="7"/>'
    for k in range(-5,6): g+=f'<path d="M{k*26},0 L{k*22},24" stroke="#5a3a1c" stroke-width="2"/>'
    # hut
    g+='<rect x="-70" y="-78" width="140" height="78" fill="#c08a52"/><path d="M-92,-76 L0,-130 L92,-76Z" fill="#6a4020"/><rect x="-24" y="-52" width="48" height="52" fill="#3e2610"/><rect x="40" y="-60" width="22" height="20" fill="#ffe08a"/>'
    for k in range(-3,4): g+=f'<path d="M{k*20},-76 v76" stroke="#a87038" stroke-width="2"/>'
    g+='<path d="M-110,-34 v34 M110,-34 v34" stroke="#5a3a1c" stroke-width="5"/><path d="M-110,-34 H-72 M110,-34 H72" stroke="#5a3a1c" stroke-width="4"/>'
    g+='</g></g>'
    return g
b+=keramba(330,640,1.05)+keramba(1280,590,.7)
# boat (perahu) - berlayar melintasi laut
def boat(x,y,s,s0,s1,dur,delay):
    g=f'<g transform="translate({x},{y}) scale({s})"><g class="sail" style="--s0:{s0}px;--s1:{s1}px;--sd:{dur}s;--sl:-{delay}s"><g class="rock">'
    g+='<ellipse cx="0" cy="30" rx="170" ry="14" fill="#0a2e4d" opacity=".35"/>'
    g+='<path d="M-160,-8 Q-130,34 -80,34 L90,34 Q140,34 175,-14 Q80,10 -160,-8Z" fill="#2c6fb0"/><path d="M-160,-8 Q0,16 175,-14 L172,-24 Q0,0 -158,-20Z" fill="#f4eee0"/>'
    g+='<path d="M-110,-24 L-60,-70 L40,-70 L70,-24Z" fill="#d9482f"/><rect x="-44" y="-58" width="26" height="20" fill="#ffe08a"/>'
    g+='<path d="M120,-20 L190,-96" stroke="#5a3a1c" stroke-width="7" stroke-linecap="round"/><path d="M190,-96 Q230,-70 224,-22" stroke="#fff" stroke-width="2" fill="none" opacity=".8"/>'
    g+='<path d="M-150,-14 Q-190,-60 -196,-72" stroke="#5a3a1c" stroke-width="5"/>'
    g+='<ellipse class="foam" cx="-190" cy="34" rx="70" ry="6" fill="#fff"/><ellipse class="foam" style="animation-delay:-.6s" cx="-280" cy="38" rx="48" ry="5" fill="#fff"/><ellipse class="foam" style="animation-delay:-1s" cx="-350" cy="40" rx="30" ry="4" fill="#fff"/>'
    return g+'</g></g></g>'
b+=boat(940,700,1.15,-1060,800,60,34)
# ikan kecil berenang di bawah permukaan (bayangan)
def swimmer(x0,y,s,flip,dur,delay):
    body='<path d="M-30,0 Q-4,-16 26,-4 Q-4,14 -30,0Z" fill="#d8f0ff"/><g class="wig"><path d="M-28,0 L-46,-12 L-42,0 L-46,12Z" fill="#d8f0ff"/></g>'
    return f'<g transform="translate({x0},{y}) scale({s*flip},{s})" opacity=".38"><g class="swim" style="--sd:{dur}s;--sl:-{delay}s">{body}</g></g>'
for (x0,y,s,fl,d,dl) in [(-200,610,.7,1,38,5),(-200,650,.55,1,46,22),(1800,690,.8,-1,34,9),(-200,760,1.0,1,40,17),(1800,830,1.2,-1,48,30),(-200,860,.9,1,36,2),(1800,740,.6,-1,52,14)]:
    b+=swimmer(x0,y,s,fl,d,dl)
# ikan melompat
FISH='<path d="M-46,0 Q-6,-30 40,-6 Q-6,26 -46,0Z" fill="url(#fishg)"/><path d="M-44,0 L-72,-20 L-66,0 L-72,20Z" fill="#f08a3a"/><path d="M-4,-24 Q10,-44 22,-14Z" fill="#f08a3a"/><circle cx="26" cy="-6" r="4" fill="#111"/><path d="M8,-14 Q16,0 8,14" stroke="#fff" stroke-width="2" fill="none" opacity=".7"/>'
def ring(x,y,s,cls,dur,delay):
    return f'<ellipse class="{cls}" cx="{x:.0f}" cy="{y:.0f}" rx="{46*s:.0f}" ry="{9*s:.0f}" fill="none" stroke="#fff" stroke-width="3" style="animation-duration:{dur}s;animation-delay:-{delay}s"/>'
def jumpfish(x,y,s,dirn,dur,delay):
    o=ring(x,y+4,s,'ringA',dur,delay)+ring(x+160*s*dirn,y+4,s,'ringB',dur,delay)
    o+=f'<g transform="translate({x},{y}) scale({s*dirn},{s})"><g class="jump" style="--jd:{dur}s;--jl:-{delay}s">{FISH}</g></g>'
    return o
b+=jumpfish(1060,560,.9,1,7,0)+jumpfish(720,640,1.1,-1,9,3.5)+jumpfish(1330,610,.8,-1,8,5)+jumpfish(300,780,1.3,1,10,6)
# foreground bamboo poles + ripple
for x in (80,150,1500):
    b+=f'<g class="sway"><rect x="{x}" y="640" width="16" height="320" fill="#a9b84a" rx="6"/><path d="M{x},700 h16M{x},800 h16" stroke="#6b7a28" stroke-width="4"/><path d="M{x+8},640 q50,-30 90,10" stroke="#6bb04a" stroke-width="5" fill="none"/></g>'
b+=sparkles(9,40,300,1300,540,860,"#fff6c8",3.2)
open('/home/claude/agronusa-web/assets/images/banner-perikanan.svg','w').write(wrap(defs,b))
