import mmap
import re

exe_path = r"C:\Program Files\WindowsApps\Microsoft.MinecraftUWP_1.26.5203.0_x64__8wekyb3d8bbwe\Minecraft.Windows.exe"

with open(exe_path, "rb") as f:
    mm = mmap.mmap(f.fileno(), 0, access=mmap.ACCESS_READ)
    
    for needle in [b"InitialConnection-148", b"InitialConnection", b"GameSetupShutdown"]:
        print(f"=== Searching for {needle} ===")
        pos = 0
        count = 0
        while count < 10:
            idx = mm.find(needle, pos)
            if idx == -1:
                break
            count += 1
            print(f"Match at 0x{idx:X}")
            start = max(0, idx - 200)
            end = min(len(mm), idx + 200)
            chunk = mm[start:end]
            printable = "".join(chr(b) if 32 <= b < 127 else "." for b in chunk)
            print("Context:", printable)
            pos = idx + len(needle)

