import random, math
W,H=1600,900
def smooth(pts, close_bottom=True, y_bottom=H+20):
    # Catmull-Rom -> cubic bezier
    d=f"M{pts[0][0]:.1f},{pts[0][1]:.1f}"
    for i in range(len(pts)-1):
        p0=pts[max(i-1,0)];p1=pts[i];p2=pts[i+1];p3=pts[min(i+2,len(pts)-1)]
        c1=(p1[0]+(p2[0]-p0[0])/6,p1[1]+(p2[1]-p0[1])/6)
        c2=(p2[0]-(p3[0]-p1[0])/6,p2[1]-(p3[1]-p1[1])/6)
        d+=f"C{c1[0]:.1f},{c1[1]:.1f} {c2[0]:.1f},{c2[1]:.1f} {p2[0]:.1f},{p2[1]:.1f}"
    if close_bottom: d+=f"L{pts[-1][0]},{y_bottom}L{pts[0][0]},{y_bottom}Z"
    return d
def ridge(seed,base,amp,n=9,x0=-40,x1=W+40,rough=1.0):
    r=random.Random(seed); pts=[]
    for i in range(n):
        x=x0+(x1-x0)*i/(n-1)
        y=base+amp*(math.sin(i*1.3+seed)*0.6+r.uniform(-1,1)*0.5*rough)
        pts.append((x,y))
    return pts
def hills(seed,base,amp,fill,n=9,rough=1.0,op=1):
    return f'<path d="{smooth(ridge(seed,base,amp,n,rough=rough))}" fill="{fill}" opacity="{op}"/>'
def cloud(x,y,s=1,op=.9,dur=80,delay=0,drift=120):
    r=random.Random(int(x+y))
    blobs=""
    for i in range(7):
        cx=(i-3)*38*s+r.uniform(-8,8); cy=r.uniform(-14,10)*s; rx=r.uniform(50,85)*s; ry=r.uniform(20,34)*s
        blobs+=f'<ellipse cx="{cx:.0f}" cy="{cy:.0f}" rx="{rx:.0f}" ry="{ry:.0f}"/>'
    return f'<g transform="translate({x},{y})"><g class="drift" style="animation-duration:{dur}s;animation-delay:-{delay}s;--dx:{drift}px"><g fill="#fff" opacity="{op}" filter="url(#soft)">{blobs}</g></g></g>'
def birds(pts,color="#2b2b2b",op=.75):
    out=""
    for (x,y,s) in pts:
        out+=f'<path d="M{x-14*s},{y} Q{x-7*s},{y-10*s} {x},{y} Q{x+7*s},{y-10*s} {x+14*s},{y}" fill="none" stroke="{color}" stroke-width="{2.2*s:.1f}" stroke-linecap="round" opacity="{op}"/>'
    return f'<g class="flap">{out}</g>'
def sparkles(seed,n,x0,x1,y0,y1,color="#fff",rmax=3.2):
    r=random.Random(seed); o=""
    for i in range(n):
        o+=f'<circle cx="{r.uniform(x0,x1):.0f}" cy="{r.uniform(y0,y1):.0f}" r="{r.uniform(1,rmax):.1f}" fill="{color}" class="tw" style="animation-delay:-{r.uniform(0,6):.1f}s;animation-duration:{r.uniform(3,7):.1f}s"/>'
    return o
COMMON_DEFS='''
<filter id="soft" x="-30%" y="-60%" width="160%" height="220%"><feGaussianBlur stdDeviation="9"/></filter>
<filter id="blur2" x="-20%" y="-20%" width="140%" height="140%"><feGaussianBlur stdDeviation="2.2"/></filter>
<filter id="blur6"><feGaussianBlur stdDeviation="6"/></filter>
<filter id="blur20" x="-50%" y="-50%" width="200%" height="200%"><feGaussianBlur stdDeviation="22"/></filter>
<filter id="grain" x="0" y="0" width="100%" height="100%"><feTurbulence type="fractalNoise" baseFrequency=".9" numOctaves="2" seed="4"/><feColorMatrix values="0 0 0 0 0  0 0 0 0 0  0 0 0 0 0  0 0 0 .5 0"/></filter>
<radialGradient id="vig" cx=".5" cy=".5" r=".75"><stop offset=".55" stop-color="#000" stop-opacity="0"/><stop offset="1" stop-color="#000" stop-opacity=".38"/></radialGradient>
'''
STYLE='''<style>
.drift{animation:dr linear infinite alternate}@keyframes dr{to{transform:translateX(var(--dx))}}
.tw{animation:tw ease-in-out infinite}@keyframes tw{0%,100%{opacity:.15}50%{opacity:.95}}
.flap{animation:fl 5s ease-in-out infinite alternate}@keyframes fl{to{transform:translate(60px,-14px)}}
.sway{transform-box:fill-box;transform-origin:50% 100%;animation:sw 4.5s ease-in-out infinite alternate}@keyframes sw{from{transform:rotate(-2.2deg)}to{transform:rotate(2.2deg)}}
.glow{animation:gl 7s ease-in-out infinite alternate}@keyframes gl{to{opacity:.72}}
.bob{animation:bb 4s ease-in-out infinite alternate}@keyframes bb{to{transform:translateY(7px)}}
.rip{animation:rp 6s ease-in-out infinite alternate}@keyframes rp{to{transform:translateX(26px)}}
.walk{animation:walk var(--wd,26s) ease-in-out var(--wl,0s) infinite}
@keyframes walk{0%{transform:translateX(0) scaleX(1)}46%{transform:translateX(var(--d,200px)) scaleX(1)}50%{transform:translateX(var(--d,200px)) scaleX(-1)}96%{transform:translateX(0) scaleX(-1)}100%{transform:translateX(0) scaleX(1)}}
.legA,.legB{transform-box:fill-box;transform-origin:50% 6%}
.legA{animation:legA .9s ease-in-out infinite alternate}.legB{animation:legB .9s ease-in-out infinite alternate}
@keyframes legA{from{transform:rotate(-15deg)}to{transform:rotate(15deg)}}@keyframes legB{from{transform:rotate(15deg)}to{transform:rotate(-15deg)}}
.step{animation:step .45s ease-in-out infinite alternate}@keyframes step{to{transform:translateY(-3px)}}
.tail{transform-box:fill-box;transform-origin:100% 0;animation:tail 1.5s ease-in-out infinite alternate}@keyframes tail{from{transform:rotate(-16deg)}to{transform:rotate(16deg)}}
.nod{animation:nod 5s ease-in-out infinite}@keyframes nod{0%,100%{transform:rotate(0)}35%,60%{transform:rotate(13deg)}}
.peck{transform-origin:10px 18px;animation:peck 3.4s ease-in-out infinite}
@keyframes peck{0%,52%,100%{transform:rotate(0)}58%{transform:rotate(26deg)}64%{transform:rotate(2deg)}70%{transform:rotate(26deg)}78%{transform:rotate(0)}}
.sail{animation:sail var(--sd,60s) linear var(--sl,0s) infinite}@keyframes sail{from{transform:translateX(var(--s0))}to{transform:translateX(var(--s1))}}
.rock{transform-origin:0 24px;animation:rock 3.2s ease-in-out infinite alternate}@keyframes rock{from{transform:rotate(-1.8deg) translateY(0)}to{transform:rotate(1.8deg) translateY(5px)}}
.foam{transform-box:fill-box;transform-origin:100% 50%;animation:foam 1.3s ease-in-out infinite alternate}@keyframes foam{from{opacity:.15;transform:scaleX(.6)}to{opacity:.75;transform:scaleX(1.1)}}
.jump{animation:jump var(--jd,7s) ease-in-out var(--jl,0s) infinite}
@keyframes jump{0%,52%{opacity:0;transform:translate(0,14px) rotate(-55deg)}54%{opacity:1;transform:translate(6px,0) rotate(-55deg)}64%{transform:translate(50px,-70px) rotate(-30deg)}72%{transform:translate(90px,-95px) rotate(0deg)}80%{transform:translate(130px,-65px) rotate(32deg)}87%{opacity:1;transform:translate(160px,6px) rotate(58deg)}88%,100%{opacity:0;transform:translate(160px,14px) rotate(58deg)}}
.ringA,.ringB{transform-box:fill-box;transform-origin:center;opacity:0;animation:ringA 7s ease-out infinite}
.ringB{animation-name:ringB}
@keyframes ringA{0%,53%{opacity:0;transform:scale(.3)}55%{opacity:.9}68%{opacity:0;transform:scale(1.9)}100%{opacity:0;transform:scale(1.9)}}
@keyframes ringB{0%,86%{opacity:0;transform:scale(.3)}88%{opacity:.9}99%{opacity:0;transform:scale(1.9)}100%{opacity:0}}
.swim{animation:swim var(--sd,40s) linear var(--sl,0s) infinite}@keyframes swim{from{transform:translateX(0)}to{transform:translateX(2000px)}}
.wig{transform-box:fill-box;transform-origin:100% 50%;animation:wig .5s ease-in-out infinite alternate}@keyframes wig{from{transform:rotate(-9deg)}to{transform:rotate(9deg)}}
.fly{animation:fly var(--fdur,10s) ease-in-out var(--fdl,0s) infinite}
@keyframes fly{0%{transform:translate(calc(var(--fd)*-1),0) rotate(0)}25%{transform:translate(calc(var(--fd)*-.0),-14px) rotate(5deg)}50%{transform:translate(var(--fd),0) rotate(0)}75%{transform:translate(0,-14px) rotate(-5deg)}100%{transform:translate(calc(var(--fd)*-1),0) rotate(0)}}
.prop{transform-box:fill-box;transform-origin:center;animation:prop .09s linear infinite alternate}@keyframes prop{from{transform:scaleX(1)}to{transform:scaleX(.3)}}
.fall{animation:fall 1.1s linear infinite}@keyframes fall{0%{opacity:0;transform:translateY(-24px)}20%{opacity:.9}100%{opacity:0;transform:translateY(46px)}}
@media (prefers-reduced-motion:reduce){*{animation:none!important}}
</style>'''
def wrap(defs,body,extra_style=""):
    return f'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {W} {H}" preserveAspectRatio="xMidYMid slice"><defs>{COMMON_DEFS}{defs}</defs>{STYLE}{body}<rect width="{W}" height="{H}" fill="url(#vig)"/><rect width="{W}" height="{H}" filter="url(#grain)" opacity=".09"/></svg>'
