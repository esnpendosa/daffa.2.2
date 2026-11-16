from lib import *
r=random.Random(7)
defs='''
<linearGradient id="sky" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#5aa7d6"/><stop offset=".45" stop-color="#bfe3df"/><stop offset=".75" stop-color="#ffe3b5"/><stop offset="1" stop-color="#ffd2a0"/></linearGradient>
<radialGradient id="sunG"><stop offset="0" stop-color="#fffbe6"/><stop offset=".3" stop-color="#ffefb8" stop-opacity=".7"/><stop offset="1" stop-color="#ffd9a0" stop-opacity="0"/></radialGradient>
<linearGradient id="mist" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#fff" stop-opacity="0"/><stop offset=".5" stop-color="#fff" stop-opacity=".55"/><stop offset="1" stop-color="#fff" stop-opacity="0"/></linearGradient>
<linearGradient id="path" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#c58a54"/><stop offset="1" stop-color="#8a5a32"/></linearGradient>
<linearGradient id="leaf" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#6fc24a"/><stop offset="1" stop-color="#1f6b2e"/></linearGradient>
<radialGradient id="cherry" cx=".35" cy=".3"><stop offset="0" stop-color="#ff8a6a"/><stop offset=".5" stop-color="#e0262b"/><stop offset="1" stop-color="#8f0f1a"/></radialGradient>
<linearGradient id="ray" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#fff4c0" stop-opacity=".6"/><stop offset="1" stop-color="#fff4c0" stop-opacity="0"/></linearGradient>
'''
b=f'<rect width="{W}" height="{H}" fill="url(#sky)"/>'
b+='<circle cx="430" cy="330" r="420" fill="url(#sunG)" class="glow"/><circle cx="430" cy="330" r="54" fill="#fffdf0"/>'
for a in (-30,-14,2,18,34,50):
    b+=f'<polygon points="430,330 {430+math.tan(math.radians(a-2.5))*1000:.0f},1300 {430+math.tan(math.radians(a+2.5))*1000:.0f},1300" fill="url(#ray)" opacity=".5"/>'
b+=cloud(900,150,1.5,.8,100,20,-110)+cloud(1350,260,1.1,.7,90,50,100)+cloud(200,120,1,.7,120,0,90)
b+=birds([(1000,260,.9),(1050,240,.8),(1100,270,.7)])
b+=hills(2,470,50,"#7fb6a8",8,1.3,.85)+hills(4,520,40,"#5a9d86",9,1.2,.95)
b+=f'<rect y="430" width="{W}" height="150" fill="url(#mist)"/>'
b+=hills(6,570,36,"#3f8a5a",9,1.2)
# palm oil / trees field rows on far hill
def tree(x,y,s,c1,c2,trunk="#4a3220"):
    t=f'<rect x="{x-2*s}" y="{y-14*s}" width="{4*s}" height="{16*s}" fill="{trunk}" rx="{2*s}"/>'
    t+=f'<ellipse cx="{x}" cy="{y-30*s}" rx="{22*s}" ry="{20*s}" fill="{c1}"/><ellipse cx="{x-7*s}" cy="{y-36*s}" rx="{13*s}" ry="{11*s}" fill="{c2}" opacity=".9"/>'
    t+=f'<ellipse cx="{x}" cy="{y+2*s}" rx="{20*s}" ry="{4*s}" fill="#0b2d18" opacity=".25"/>'
    return t
# rows in perspective: converge to vanishing point (800,590)
vp=(800,585)
for row,(ex,y1) in enumerate([(-300,900),(-80,900),(180,900),(430,900),(1170,900),(1420,900),(1680,900),(1900,900)]):
    n=9
    for k in range(n):
        t=(k/(n-1))**1.9
        x=vp[0]+(ex-vp[0])*t; y=vp[1]+(y1-vp[1])*t
        s=.35+t*2.1
        c=("#1f6d34","#3f9a45") if row%2==0 else ("#256f3a","#4aa64a")
        b+=tree(x,y,s,*c)
# ground path center
b+=f'<path d="M740,585 L860,585 L1140,900 L460,900Z" fill="url(#path)" opacity=".95"/>'
b+=f'<path d="M740,585 L860,585 L1140,900 L1000,900Z" fill="#fff" opacity=".07"/>'
# light dapples
b+=sparkles(5,34,100,1500,430,780,"#fff6c8",3.6)
# foreground coffee branch (right)
def leaf(x,y,a,s,fill="url(#leaf)"):
    return f'<g transform="translate({x},{y}) rotate({a}) scale({s})"><path d="M0,0 C30,-60 120,-62 190,0 C120,62 30,60 0,0Z" fill="{fill}"/><path d="M4,0 L180,0" stroke="#d6f2a8" stroke-width="3" opacity=".7"/><path d="M60,0 L100,-26 M60,0 L100,26 M110,0 L145,-20 M110,0 L145,20" stroke="#d6f2a8" stroke-width="2" opacity=".5"/><path d="M20,-10 C70,-40 130,-36 170,-6" stroke="#fff" stroke-width="5" opacity=".18" fill="none"/></g>'
def cluster(x,y,s):
    o=""
    for dx,dy in ((0,0),(26,8),(10,30),(-18,22),(34,34)):
        o+=f'<circle cx="{x+dx*s}" cy="{y+dy*s}" r="{15*s}" fill="url(#cherry)"/><circle cx="{x+dx*s-5*s}" cy="{y+dy*s-6*s}" r="{4*s}" fill="#fff" opacity=".75"/>'
    return o
br='<path d="M1700,980 C1500,760 1320,650 1130,590" stroke="#5b3b22" stroke-width="16" fill="none" stroke-linecap="round"/>'
for (x,y,a,s) in [(1140,590,-35,1.0),(1160,596,40,1.0),(1260,650,-60,1.1),(1280,660,25,1.2),(1380,720,-70,1.3),(1400,740,15,1.3),(1500,810,-80,1.4),(1520,830,5,1.3)]:
    br+=leaf(x,y,a,s)
br+=cluster(1250,700,1.2)+cluster(1355,770,1.3)+cluster(1190,640,1.0)+cluster(1470,860,1.4)
b+=f'<g class="sway" style="transform-origin:100% 100%">{br}</g>'
# blurred front-left leaves
fl=leaf(-60,880,-50,1.7)+leaf(40,930,-20,1.9)+leaf(-30,700,-70,1.4)
b+=f'<g filter="url(#blur6)" opacity=".92">{fl}</g>'
open('/home/claude/agronusa-web/assets/images/banner-perkebunan.svg','w').write(wrap(defs,b))
