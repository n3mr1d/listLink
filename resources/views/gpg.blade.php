<x-app.layouts title="GPG Public Key"
    description="Verify official communications and secure your interactions with {{ config('app.name') }} using our PGP/GPG public key.">

    <div style="max-width:800px;margin:1rem auto 3rem;padding:0 1rem;">

        {{-- Header Section --}}
        <div
            style="margin-bottom:2rem;text-align:center;padding:2rem 0;border-bottom:1px solid var(--color-gh-border);">
            <div
                style="width:3.5rem;height:3.5rem;background:rgba(88,166,255,.1);border-radius:1rem;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;color:var(--color-gh-accent);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                </svg>
            </div>
            <h1 style="font-size:1.75rem;font-weight:900;color:#fff;margin:0 0 .5rem;">Verification Protocol</h1>
            <p style="color:var(--color-gh-dim);font-size:.85rem;max-width:500px;margin:0 auto;">Official GPG Public Key
                for {{ config('site.whoami') }}. All automated emails and communications are signed with this identity.
            </p>
        </div>

        <div style="display:grid;grid-template-columns:1fr;gap:2rem;">

            {{-- Key Identity --}}
            <div style="border:1px solid var(--color-gh-border);border-radius:.6rem;overflow:hidden;">
                <div
                    style="padding:1rem;border-bottom:1px solid var(--color-gh-border);display:flex;justify-content:space-between;align-items:center;">
                    <span
                        style="font-size:.65rem;font-weight:800;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.1em;">Identity
                        Metadata</span>
                    <span
                        style="font-size:.6rem;font-weight:700;color:#4ade80;text-transform:uppercase;padding:.2rem .5rem;border:1px solid rgba(74,222,128,.3);border-radius:.25rem;">Active
                        State</span>
                </div>
                <div style="padding:1.25rem;display:flex;flex-direction:column;gap:1rem;">
                    <div>
                        <span
                            style="display:block;font-size:.65rem;font-weight:700;color:var(--color-gh-dim);text-transform:uppercase;margin-bottom:.3rem;">User
                            ID</span>
                        <code
                            style="color:var(--color-gh-accent);font-weight:700;font-size:.9rem;">{{ config('site.whoami')}} <{{ config('site.contact_email') }}></code>
                    </div>
                    <div>
                        <span
                            style="display:block;font-size:.65rem;font-weight:700;color:var(--color-gh-dim);text-transform:uppercase;margin-bottom:.3rem;">Fingerprint</span>
                        <code
                            style="color:#fff;font-family:monospace;font-size:.8rem;word-break:break-all;display:block;padding:.75rem;border-radius:.35rem;border:1px dashed var(--color-gh-border);">
                           E45A 9CE8 0FDD 91B9 9F2B 7C35 5E47 775F AEFA 21E1
                        </code>
                    </div>
                </div>
            </div>

            {{-- Key Block --}}
            <div style="border:1px solid var(--color-gh-border);border-radius:.6rem;overflow:hidden;">
                <div
                    style="padding:1rem;border-bottom:1px solid var(--color-gh-border);display:flex;justify-content:space-between;align-items:center;">
                    <span
                        style="font-size:.65rem;font-weight:800;color:var(--color-gh-dim);text-transform:uppercase;letter-spacing:.1em;">Public
                        Key Block</span>
                    <div style="display:flex;gap:.5rem;">
                        <button onclick="copyToClipboard()"
                            style="background:transparent;border:1px solid var(--color-gh-border);color:var(--color-gh-dim);padding:.3rem .6rem;font-size:.65rem;font-weight:800;border-radius:.3rem;cursor:pointer;display:flex;align-items:center;gap:.3rem;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5">
                                <rect x="9" y="9" width="13" height="13" rx="2" ry="2" />
                                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
                            </svg>
                            Copy Data
                        </button>
                    </div>
                </div>
                <div style="padding:1.25rem;">
                    <pre id="gpg-key"
                        style="margin:0;padding:1rem;border-radius:.4rem;color:rgba(230,237,243,.8);font-size:.72rem;line-height:1.4;overflow-x:auto;font-family:monospace;border:1px solid rgba(48,54,61,0.5);">
-----BEGIN PGP PUBLIC KEY BLOCK-----

mQGNBGncJUwBDACmczwY5/lZdZbl94a+tTcnN+znHyhje9ARW3TN01Kft98GT23p
MH49Od08rb0pKeld28GC96Gi7iumleBvnNYFHx2UNDuACFb/X2prvyHtVoLVLBRk
3nRTMuzSV+0FvZpuUuKioW0Jpm23CF8/BdeNeXPGLJBpPwyyaQRhZ3pNXCCDXGJ7
tFN6cuGAQjSbsejLsp88TV3Q4KSAfah/JG9ZIj7tML2eyIKF9Vq+5U2Gb+BJrSI9
8T4KKZCs0N6QETRqTl2nWBiGvqvfttyUmd74tPhwT9I1BdigFWDTHI94aWWF9PqF
U7e5lyVxZ1ScNFPavV11q/hdee5ucwcsstzP5rqN7Hy63DkCOevioFZX6joqaUuZ
7ikMG3T70pz4WOn7gq5reXgZWFLanD8hqgtHECmUJqqywB8X0offPMTb5GDSNjFA
7QXFsYytMeeQRaNVh0UBwtgT6xTRMtEQPi4i0xzgM8kIuB3ZS1p0CTj9R1r2L9gM
DPM5sdECUxi7O+cAEQEAAbRfVHJlaXhub3ggKERyaWZ0aW5nIHRocm91Z2ggaWRl
bnRpdGllcy4gWW91IHNlZSBhIG5hbWUsIEkgc2VlIGEgbWFzay4pIDxUcmVpeG5v
eEBwcm90b25tYWlsLmNvbT6JAc4EEwEKADgWIQTkWpzoD92RuZ8rfDVeR3dfrvoh
4QUCadwlTAIbAwULCQgHAgYVCgkICwIEFgIDAQIeAQIXgAAKCRBeR3dfrvoh4dsp
DACKnh+Pd/tgIs539tExdUerv04pNfreRHnDgPbivo+TlYhVQA9vtIl1wmoLk/ZY
PfCv0OK+/8t5YLxyalLQWDsUQLSaQ599e1EIMn3R30VM58JQFtBp+tfDzIvzk/q6
dfBlM47qLp8L9KIqRwBlWpNmgc7THvsL3ZjT47/WowVZr6qMHmDWVRydBwhzfgBu
BQu/5QmtwCxPPMLluDJwyBWZhBKhj43WrPkBmN0liSKiR6bhFMAsD6VfE62pgLpH
XLh4p4tJt7lilGiDgkSM1eDEpGSn7L1C2o7KNoT3vi9QYrUkdfYo2rmgw8tBG+ga
Isz1tTkUGVWGy7aY08glXTQmj+1JU23lLi85sw3fOz/+9iARDncq2PX/m57u0G79
P0i0yq4Hm3uN0iqh9kul3TeXymwltngp5iTlPWMBclMFTPlLV/yHztizP0cc0hVG
ox5+81Vrb6gOLXBgQSzvBkJLTLPEweX/qUeOB0OteRe2N38n34b5kJjdFuKKvBgS
2S65AY0EadwlTAEMANRHAZtgFVQXu1Zd/6RJmQtjlu/YFWZPLZ2t5XNMPvBirRuu
QIC17QTQqEk2Q2z1v821E68T7dbCrxj6obKjDoqECqyf1JoYHP6wehK0MVjbxJy+
r5MnofCWOw8IG6raBnWZ+GFdHX/eRBPkhEtBkfxyykREVrK4Ch4G63t9ZLSE8ymB
qmEBWkaq700HGDKZAENatYnGPxv2X9YVmZqYDvLIllnWuuazoWmjTVSsXGghK+V/
NsemzltykgpRkSp/29W+HdPFy89Wibtbw4HL3jDe7ReibwwbredU9tZk1Sxb1DBz
MGPnByii8Z9/lx6z/SF6lKWTuHQIl2vHdy+DZ5v1mSRZFBhXM7fBsN5su2GWwJGZ
gZz22/rE/Xk3+Fj6RjlOX+aIOtgZAb2ca7D1TmepaXRRbhKHaP7JYSbkxk40IwrT
93qfP1/szWDaz68ETuPHVTJQs9meBZLOkE9CwkqVGGi7zc4UwzrtTXca6KYWjbtF
MYgHK8DmRhH9ROpanwARAQABiQG2BBgBCgAgFiEE5Fqc6A/dkbmfK3w1Xkd3X676
IeEFAmncJUwCGwwACgkQXkd3X676IeHRvwv8DAnlza0V68E+NmNgyF151qw4JiM1
SU8nSkV4sDAvrPp4fgvfBmZZVXArKo1a7tvbBWLi2ynyrYj30F7dIHkoIxFYbgL9
UWYfuILBkwv3Z1DrqXn/+75QkoqaQMRTUWdAu6dAAwDPwF/Ath4ks7yvKYtzg/dw
e34UQ6/D/TL3pIX2V5o9m3XedDQPnaKJdhCeOK3fu2LawOZPK+JaC5VYebUbiWQO
FcOCVo9Rmo7IiBD1zL9S8rhD2Bv5tooaEUy9TmcaX/So1h7l6bqwcv3fYMbL1D4+
z4ZixU5FvWIHO+QQvuW0iGiBxJgUYbytHL/ITHzMt+FphPLDDweGotKcK+9m+h2T
3bAnNXFl/bIEgvXR8vm19DyCuU50Y+JUl48wB5eN3VY6OI17whUpUYnyMTp6lLQC
j9dpubxT6mazTsNv0l+Sn8FK+Mdi9ZxbHugSGIpeJERW3kY4zn20VMmq0CZo4TyJ
QFf0RGAhWX0xLU9BKUXzLVKO/DB6bIQWG5Iv
=56H3
-----END PGP PUBLIC KEY BLOCK-----
                    </pre>
                </div>
            </div>

            {{-- Instructions --}}
            <div style="display:flex;gap:1rem;border:1px solid rgba(35,134,54,.3);padding:1.25rem;border-radius:.6rem;">
                <div style="color:var(--color-accent-green);flex-shrink:0;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                    </svg>
                </div>
                <div>
                    <h4 style="margin:0 0 .25rem;font-size:.75rem;font-weight:800;color:#fff;text-transform:uppercase;">
                        Encryption Tip</h4>
                    <p style="margin:0;font-size:.72rem;color:var(--color-gh-dim);line-height:1.5;">To verify a message,
                        save the block above as <code>hiddenline.asc</code> and run
                        <code>gpg --import hiddenline.asc</code>. You can then use <code>gpg --verify [file]</code>.
                    </p>
                </div>
            </div>

        </div>
    </div>

    <script>
        function copyToClipboard() {
            const keyContent = document.getElementById('gpg-key').innerText;
            navigator.clipboard.writeText(keyContent).then(() => {
                const btn = event.currentTarget;
                const originalText = btn.innerHTML;
                btn.innerHTML = '<span style="color:#4ade80">Copied!</span>';
                setTimeout(() => { btn.innerHTML = originalText; }, 2000);
            });
        }
    </script>
</x-app.layouts>