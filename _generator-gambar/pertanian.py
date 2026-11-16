from lib import *
r=random.Random(11)
defs='''
<linearGradient id="sky" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#2f7fc8"/><stop offset=".38" stop-color="#7fc0e6"/><stop offset=".62" stop-color="#ffe2a0"/><stop offset="1" stop-color="#ffc77a"/></linearGradient>
<radialGradient id="sunG"><stop offset="0" stop-color="#fff7c8" stop-opacity="1"/><stop offset=".25" stop-color="#ffe6a0" stop-opacity=".75"/><stop offset="1" stop-color="#ffd27a" stop-opacity="0"/></radialGradient>
<linearGradient id="water" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#ffe9b0"/><stop offset=".6" stop-color="#9fd8dc"/><stop offset="1" stop-color="#5fb3c2"/></linearGradient>
<linearGradient id="haze" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#ffe8b8" stop-opacity="0"/><stop offset="1" stop-color="#ffe8b8" stop-opacity=".75"/></linearGradient>
<linearGradient id="ray" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#fff4c0" stop-opacity=".55"/><stop offset="1" stop-color="#fff4c0" stop-opacity="0"/></linearGradient>
<linearGradient id="warm" x1="1" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#ffb84a" stop-opacity=".38"/><stop offset=".6" stop-color="#ffb84a" stop-opacity="0"/></linearGradient>
'''
b=f'<rect width="{W}" height="{H}" fill="url(#sky)"/>'
b+='<circle cx="1180" cy="400" r="420" fill="url(#sunG)" class="glow"/><circle cx="1180" cy="400" r="62" fill="#fffbe0"/>'
# rays
for a in (-62,-42,-24,-8,10,28):
    b+=f'<polygon points="1180,400 {1180+math.tan(math.radians(a-3))*900:.0f},1300 {1180+math.tan(math.radians(a+3))*900:.0f},1300" fill="url(#ray)" opacity=".45" transform="rotate(0)"/>'
b+=cloud(250,170,1.5,.95,90,10,140)+cloud(760,110,1.1,.85,110,40,-90)+cloud(1400,200,1.3,.8,100,0,-120)+cloud(520,300,.8,.7,120,60,100)
b+=birds([(420,230,1),(470,205,.8),(520,245,.9),(900,150,.7),(940,135,.6)])
# mountains
b+=hills(3,500,40,"#8fb4c9",8,1.2,.75)
b+=hills(5,535,45,"#6f9f95",9,1.2,.9)
b+=f'<rect y="440" width="{W}" height="140" fill="url(#haze)"/>'
b+=hills(8,560,28,"#4f8c4a",10,1.4)
# palms on ridge
def palm(x,y,s):
    t=f'<path d="M{x},{y} q{8*s},{-60*s} {3*s},{-120*s}" stroke="#3b2a1a" stroke-width="{5*s}" fill="none" stroke-linecap="round"/>'
    tx,ty=x+3*s,y-120*s
    for a in (-160,-125,-90,-55,-20,15,-200):
        ex=tx+math.cos(math.radians(a))*58*s; ey=ty+math.sin(math.radians(a))*34*s+16*s
        t+=f'<path d="M{tx},{ty} Q{(tx+ex)/2},{ty-26*s} {ex:.0f},{ey:.0f}" stroke="#1f5a2c" stroke-width="{4*s}" fill="none" stroke-linecap="round"/>'
    return f'<g class="sway">{t}</g>'
b+=palm(180,585,1.1)+palm(260,590,.8)+palm(1450,560,1.0)+palm(1390,566,.7)
# terraces
cols=[("#5fae45","#86c457"),("#2f8f3c","#5fb04a"),("water",None),("#3f9a3d","#7bc653"),("water",None),("#2d8a3a","#58b04a"),("#76be4d","#a6d65e"),("water",None),("#2a7f35","#4fa646")]
y=590
for i,(c,c2) in enumerate(cols):
    amp=10+i*5; base=y
    pts=ridge(20+i*7,base,amp,8+i%3,rough=.8)
    fill="url(#water)" if c=="water" else c
    b+=f'<path d="{smooth(pts)}" fill="{fill}"/>'
    if c=="water":
        # reflection sheen + sun glint
        b+=f'<path d="{smooth([(x,yy+amp*.6) for x,yy in pts],False)}" stroke="#fff6cf" stroke-width="{2+i*.4:.1f}" fill="none" opacity=".7"/>'
        b+=f'<ellipse cx="{1180-i*40}" cy="{base+amp+10}" rx="{140+i*30}" ry="{6+i}" fill="#fff4c0" opacity=".55" class="rip" filter="url(#blur2)"/>'
    else:
        for k in range(1,5+i//2):
            off=k*(6+i*1.6)
            b+=f'<path d="{smooth([(x,yy+off) for x,yy in pts],False)}" stroke="{c2}" stroke-width="{1.4+i*.28:.1f}" fill="none" opacity=".85"/>'
    # terrace edge highlight
    b+=f'<path d="{smooth(pts,False)}" stroke="#d8f08a" stroke-width="2" fill="none" opacity=".55"/>'
    y+=28+i*11
b+=f'<rect width="{W}" height="{H}" fill="url(#warm)"/>'
# small hut
b+='<g transform="translate(300,640)"><path d="M0,0h70v-34h-70z" fill="#a9763f"/><path d="M-10,-34 L35,-70 L80,-34z" fill="#7a4b22"/><rect x="26" y="-22" width="18" height="22" fill="#4a2c12"/></g>'
# foreground rice stalks
def stalk(x,yb,h,s,col,pan):
    lean=r.uniform(-45,45)*s; tx=x+lean; ty=yb-h
    d=f'<path d="M{x},{yb} Q{x+lean*.2},{yb-h*.6} {tx:.0f},{ty:.0f}" stroke="{col}" stroke-width="{3.2*s:.1f}" fill="none" stroke-linecap="round"/>'
    # drooping panicle
    d+=f'<path d="M{tx:.0f},{ty:.0f} q{26*s*(1 if lean>0 else -1)},{-4*s} {34*s*(1 if lean>0 else -1)},{34*s}" stroke="{pan}" stroke-width="{3*s:.1f}" fill="none" stroke-linecap="round"/>'
    for k in range(9):
        sg=1 if lean>0 else -1
        px=tx+sg*(8+k*3.6)*s; py=ty+(k*3.6)*s*1.1-2*s
        d+=f'<ellipse cx="{px:.0f}" cy="{py:.0f}" rx="{5*s:.1f}" ry="{2.6*s:.1f}" fill="{pan}" transform="rotate({35*sg} {px:.0f} {py:.0f})"/>'
    # leaf blade
    d+=f'<path d="M{x},{yb-h*.35} q{-60*s},{-30*s} {-95*s},{40*s}" stroke="{col}" stroke-width="{2.4*s:.1f}" fill="none" stroke-linecap="round"/>'
    return d
mid=""
for i in range(34):
    mid+=f'<g class="sway" style="animation-delay:-{r.uniform(0,4):.1f}s">'+stalk(r.uniform(-20,W+20),H+10,r.uniform(150,230),.85,"#5a9a2c","#e0a823")+'</g>'
b+=f'<g opacity=".95">{mid}</g>'
near=""
for i in range(16):
    near+=f'<g class="sway" style="animation-delay:-{r.uniform(0,4):.1f}s">'+stalk(r.uniform(-30,W+30),H+40,r.uniform(260,380),1.7,"#3f7d1f","#f0b82a")+'</g>'
b+=f'<g filter="url(#blur2)">{near}</g>'
b+=sparkles(2,26,100,1500,420,820,"#fff6c8",3.4)
open('/home/claude/agronusa-web/assets/images/banner-pertanian.svg','w').write(wrap(defs,b))
