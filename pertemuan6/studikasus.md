# Studi Kasus API Testing dengan Postman: Sistem Perpustakaan (FastAPI) – Lengkap dengan Data Dummy

## 📘 Deskripsi Studi Kasus

Anda adalah seorang QA Engineer yang bertugas menguji **API Sistem Perpustakaan** yang dibangun dengan **FastAPI (Python)**. API ini memiliki 23 endpoint yang mencakup autentikasi, manajemen user, buku, peminjaman, pengembalian, denda, dan laporan. Anda harus membuat **collection Postman** yang berisi minimal **20 test case** (positif dan negatif) untuk menguji seluruh fungsionalitas. Sebelum pengujian, sistem sudah diisi dengan **data dummy minimal 20 record per tabel** (users, buku, peminjaman, denda) untuk memudahkan pengujian.

---

## 🛠️ Persiapan Lingkungan

### Prasyarat
- Python 3.9+
- Postman (versi terbaru)
- Git (opsional)

### Instalasi dan Menjalankan Server
```bash
# Buat folder proyek
mkdir perpustakaan-api
cd perpustakaan-api

# Virtual environment
python -m venv venv
source venv/bin/activate  # atau venv\Scripts\activate

# Install dependensi
pip install fastapi uvicorn sqlalchemy python-jose[cryptography] passlib[bcrypt] python-multipart

# Simpan kode main.py (lihat di bawah)
# Jalankan server
uvicorn main:app --reload --port 8000
```

Server berjalan di `http://localhost:8000`. Dokumentasi interaktif: `http://localhost:8000/docs`.

---

## 💻 Source Code Lengkap (`main.py`) dengan Seeder Data Dummy

```python
from fastapi import FastAPI, HTTPException, Depends, status
from fastapi.security import OAuth2PasswordBearer, OAuth2PasswordRequestForm
from sqlalchemy import create_engine, Column, Integer, String, Boolean, DateTime, ForeignKey, Float
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import sessionmaker, Session
from datetime import datetime, timedelta
from typing import List, Optional
from pydantic import BaseModel
import bcrypt
from jose import JWTError, jwt
import random

# ---------- Konfigurasi ----------
SECRET_KEY = "supersecretkeyforjwt"
ALGORITHM = "HS256"
ACCESS_TOKEN_EXPIRE_MINUTES = 30

# Database SQLite (file akan otomatis dibuat)
SQLALCHEMY_DATABASE_URL = "sqlite:///./perpustakaan.db"
engine = create_engine(SQLALCHEMY_DATABASE_URL, connect_args={"check_same_thread": False})
SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)
Base = declarative_base()

Berikut adalah **source code lengkap `main.py`** untuk sistem perpustakaan dengan FastAPI, mencakup:

- 23 endpoint (CRUD user, buku, peminjaman, denda, laporan, autentikasi)
- Seeder data dummy otomatis (minimal 20 record per tabel: users, buku, peminjaman, denda)
- Validasi token JWT, role-based access control (anggota & petugas)
- Database SQLite (file `perpustakaan.db` akan dibuat otomatis)

Salin kode di bawah ini ke file `main.py`, lalu jalankan dengan `uvicorn main:app --reload`.

```python
from fastapi import FastAPI, HTTPException, Depends, status
from fastapi.security import OAuth2PasswordBearer, OAuth2PasswordRequestForm
from sqlalchemy import create_engine, Column, Integer, String, Boolean, DateTime, ForeignKey, Float
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import sessionmaker, Session
from datetime import datetime, timedelta
from typing import List, Optional
from pydantic import BaseModel
import bcrypt
from jose import JWTError, jwt
import random

# ---------- Konfigurasi ----------
SECRET_KEY = "supersecretkeyforjwt"
ALGORITHM = "HS256"
ACCESS_TOKEN_EXPIRE_MINUTES = 30

# Database SQLite (file akan otomatis dibuat)
SQLALCHEMY_DATABASE_URL = "sqlite:///./perpustakaan.db"
engine = create_engine(SQLALCHEMY_DATABASE_URL, connect_args={"check_same_thread": False})
SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)
Base = declarative_base()

# ---------- Model Database ----------
class UserModel(Base):
    __tablename__ = "users"
    id = Column(Integer, primary_key=True, index=True)
    username = Column(String, unique=True, index=True)
    email = Column(String, unique=True, index=True)
    hashed_password = Column(String)
    full_name = Column(String)
    role = Column(String, default="anggota")
    is_active = Column(Boolean, default=True)

class BukuModel(Base):
    __tablename__ = "buku"
    id = Column(Integer, primary_key=True, index=True)
    judul = Column(String, index=True)
    penulis = Column(String)
    penerbit = Column(String)
    tahun = Column(Integer)
    stok = Column(Integer, default=1)

class PeminjamanModel(Base):
    __tablename__ = "peminjaman"
    id = Column(Integer, primary_key=True, index=True)
    user_id = Column(Integer, ForeignKey("users.id"))
    buku_id = Column(Integer, ForeignKey("buku.id"))
    tgl_pinjam = Column(DateTime, default=datetime.now)
    tgl_jatuh_tempo = Column(DateTime, default=lambda: datetime.now() + timedelta(days=7))
    tgl_kembali = Column(DateTime, nullable=True)
    status = Column(String, default="dipinjam")

class DendaModel(Base):
    __tablename__ = "denda"
    id = Column(Integer, primary_key=True, index=True)
    peminjaman_id = Column(Integer, ForeignKey("peminjaman.id"))
    user_id = Column(Integer, ForeignKey("users.id"))
    jumlah = Column(Float)
    dibayar = Column(Boolean, default=False)
    tgl_denda = Column(DateTime, default=datetime.now)

Base.metadata.create_all(bind=engine)

# ---------- Pydantic Schemas ----------
class UserCreate(BaseModel):
    username: str
    email: str
    password: str
    full_name: str
    role: str = "anggota"

class UserOut(BaseModel):
    id: int
    username: str
    email: str
    full_name: str
    role: str
    is_active: bool
    class Config:
        orm_mode = True

class UserUpdate(BaseModel):
    full_name: Optional[str] = None
    email: Optional[str] = None
    role: Optional[str] = None
    is_active: Optional[bool] = None

class BukuCreate(BaseModel):
    judul: str
    penulis: str
    penerbit: str
    tahun: int
    stok: int = 1

class BukuOut(BaseModel):
    id: int
    judul: str
    penulis: str
    penerbit: str
    tahun: int
    stok: int
    class Config:
        orm_mode = True

class BukuUpdate(BaseModel):
    judul: Optional[str] = None
    penulis: Optional[str] = None
    penerbit: Optional[str] = None
    tahun: Optional[int] = None
    stok: Optional[int] = None

class PeminjamanCreate(BaseModel):
    user_id: int
    buku_id: int

class PeminjamanOut(BaseModel):
    id: int
    user_id: int
    buku_id: int
    tgl_pinjam: datetime
    tgl_jatuh_tempo: datetime
    tgl_kembali: Optional[datetime]
    status: str
    class Config:
        orm_mode = True

class PeminjamanUpdate(BaseModel):
    tgl_jatuh_tempo: Optional[datetime] = None

class PengembalianRequest(BaseModel):
    peminjaman_id: int

class DendaOut(BaseModel):
    id: int
    peminjaman_id: int
    user_id: int
    jumlah: float
    dibayar: bool
    tgl_denda: datetime
    class Config:
        orm_mode = True

class DendaUpdate(BaseModel):
    dibayar: bool

class Token(BaseModel):
    access_token: str
    token_type: str

class TokenData(BaseModel):
    username: str = None

# ---------- Helper Functions ----------
def get_password_hash(password: str) -> str:
    salt = bcrypt.gensalt()
    return bcrypt.hashpw(password.encode('utf-8'), salt).decode('utf-8')

def verify_password(plain_password: str, hashed_password: str) -> bool:
    return bcrypt.checkpw(plain_password.encode('utf-8'), hashed_password.encode('utf-8'))

def authenticate_user(db: Session, username: str, password: str):
    user = db.query(UserModel).filter(UserModel.username == username).first()
    if not user or not verify_password(password, user.hashed_password):
        return False
    return user

def create_access_token(data: dict, expires_delta: Optional[timedelta] = None):
    to_encode = data.copy()
    if expires_delta:
        expire = datetime.utcnow() + expires_delta
    else:
        expire = datetime.utcnow() + timedelta(minutes=15)
    to_encode.update({"exp": expire})
    encoded_jwt = jwt.encode(to_encode, SECRET_KEY, algorithm=ALGORITHM)
    return encoded_jwt

oauth2_scheme = OAuth2PasswordBearer(tokenUrl="auth/login")

def get_current_user(token: str = Depends(oauth2_scheme), db: Session = Depends(lambda: SessionLocal())):
    credentials_exception = HTTPException(
        status_code=status.HTTP_401_UNAUTHORIZED,
        detail="Invalid authentication credentials",
        headers={"WWW-Authenticate": "Bearer"},
    )
    try:
        payload = jwt.decode(token, SECRET_KEY, algorithms=[ALGORITHM])
        username: str = payload.get("sub")
        if username is None:
            raise credentials_exception
        token_data = TokenData(username=username)
    except JWTError:
        raise credentials_exception
    user = db.query(UserModel).filter(UserModel.username == token_data.username).first()
    if user is None:
        raise credentials_exception
    return user

def get_current_petugas(current_user: UserModel = Depends(get_current_user)):
    if current_user.role != "petugas":
        raise HTTPException(status_code=403, detail="Not enough permissions")
    return current_user

# ---------- Seeder Data Dummy (minimal 20 record per tabel) ----------
def seed_dummy_data():
    db = SessionLocal()
    # Cek apakah sudah ada data
    if db.query(UserModel).count() > 0:
        return  # Sudah pernah di-seed

    # 1. Users (22: 2 petugas + 20 anggota)
    users = []
    # Petugas
    users.append(UserModel(username="petugas1", email="petugas1@lib.com", hashed_password=get_password_hash("admin123"), full_name="Petugas Satu", role="petugas"))
    users.append(UserModel(username="petugas2", email="petugas2@lib.com", hashed_password=get_password_hash("admin123"), full_name="Petugas Dua", role="petugas"))
    # Anggota
    for i in range(1, 21):
        users.append(UserModel(
            username=f"anggota{i}",
            email=f"anggota{i}@mail.com",
            hashed_password=get_password_hash("rahasia"),
            full_name=f"Anggota Ke-{i}",
            role="anggota",
            is_active=True
        ))
    db.add_all(users)
    db.commit()

    # 2. Buku (24 buku)
    judul_buku = [
        "Pemrograman Python", "Belajar FastAPI", "Database SQLAlchemy", "REST API Design",
        "Algoritma dan Struktur Data", "Jaringan Komputer", "Kecerdasan Buatan", "Data Mining",
        "Pemrograman Web", "DevOps Handbook", "Clean Code", "Refactoring", "Design Patterns",
        "The Pragmatic Programmer", "Introduction to Algorithms", "You Don't Know JS",
        "Eloquent JavaScript", "Flask Web Development", "Django for Beginners", "Machine Learning Yearning",
        "Deep Learning", "Statistika Terapan", "Matematika Diskrit", "Sistem Operasi"
    ]
    penulis_list = ["John Doe", "Jane Smith", "Robert Martin", "Eric Evans", "Martin Fowler", "Andi Wijaya", "Siti Nurhaliza", "Budi Santoso"]
    penerbit_list = ["Gramedia", "Elex Media", "Andi Offset", "O'Reilly", "Packt", "MIT Press"]
    buku_list = []
    for i in range(24):
        buku_list.append(BukuModel(
            judul=judul_buku[i],
            penulis=random.choice(penulis_list),
            penerbit=random.choice(penerbit_list),
            tahun=2015 + (i % 9),
            stok=random.randint(1, 5)
        ))
    db.add_all(buku_list)
    db.commit()

    # 3. Peminjaman (30 peminjaman)
    all_users = db.query(UserModel).filter(UserModel.role == "anggota").all()
    all_buku = db.query(BukuModel).all()
    peminjaman_list = []
    for _ in range(30):
        user = random.choice(all_users)
        buku = random.choice(all_buku)
        tgl_pinjam = datetime.now() - timedelta(days=random.randint(0, 30))
        tgl_jatuh_tempo = tgl_pinjam + timedelta(days=7)
        # status: 40% kembali, 40% dipinjam, 20% terlambat (sudah kembali tapi lewat tempo)
        r = random.random()
        if r < 0.4:
            status = "kembali"
            tgl_kembali = tgl_jatuh_tempo + timedelta(days=random.randint(-2, 5))
            if tgl_kembali > tgl_jatuh_tempo:
                status = "terlambat"
        elif r < 0.8:
            status = "dipinjam"
            tgl_kembali = None
        else:
            status = "terlambat"
            tgl_kembali = tgl_jatuh_tempo + timedelta(days=random.randint(1, 10))
        peminjaman_list.append(PeminjamanModel(
            user_id=user.id,
            buku_id=buku.id,
            tgl_pinjam=tgl_pinjam,
            tgl_jatuh_tempo=tgl_jatuh_tempo,
            tgl_kembali=tgl_kembali,
            status=status
        ))
        if status == "dipinjam":
            buku.stok -= 1
    db.add_all(peminjaman_list)
    db.commit()

    # 4. Denda (20+ denda dari peminjaman terlambat)
    all_peminjaman = db.query(PeminjamanModel).filter(PeminjamanModel.status == "terlambat").all()
    denda_list = []
    for pinjam in all_peminjaman[:25]:
        if pinjam.tgl_kembali and pinjam.tgl_kembali > pinjam.tgl_jatuh_tempo:
            terlambat_hari = (pinjam.tgl_kembali - pinjam.tgl_jatuh_tempo).days
            jumlah = terlambat_hari * 1000
            denda_list.append(DendaModel(
                peminjaman_id=pinjam.id,
                user_id=pinjam.user_id,
                jumlah=jumlah,
                dibayar=random.choice([True, False]),
                tgl_denda=pinjam.tgl_kembali
            ))
    # Jika kurang dari 20, tambahkan denda untuk peminjaman yang masih dipinjam dan sudah lewat jatuh tempo
    if len(denda_list) < 20:
        aktif_terlambat = db.query(PeminjamanModel).filter(PeminjamanModel.status == "dipinjam", PeminjamanModel.tgl_jatuh_tempo < datetime.now()).all()
        for pinjam in aktif_terlambat[:20 - len(denda_list)]:
            terlambat_hari = (datetime.now() - pinjam.tgl_jatuh_tempo).days
            jumlah = terlambat_hari * 1000
            denda_list.append(DendaModel(
                peminjaman_id=pinjam.id,
                user_id=pinjam.user_id,
                jumlah=jumlah,
                dibayar=False,
                tgl_denda=datetime.now()
            ))
    db.add_all(denda_list[:20])
    db.commit()
    print("Seeder selesai: Data dummy telah ditambahkan.")

# Panggil seeder saat startup
app = FastAPI(title="Sistem Perpustakaan API", version="1.0")

@app.on_event("startup")
def startup_event():
    seed_dummy_data()

# Dependency untuk database
def get_db():
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()

# ---------- ENDPOINTS ----------
# 1. Registrasi
@app.post("/auth/register", response_model=UserOut, status_code=201)
def register(user: UserCreate, db: Session = Depends(get_db)):
    existing = db.query(UserModel).filter((UserModel.username == user.username) | (UserModel.email == user.email)).first()
    if existing:
        raise HTTPException(status_code=400, detail="Username atau email sudah terdaftar")
    hashed = get_password_hash(user.password)
    db_user = UserModel(username=user.username, email=user.email, hashed_password=hashed, full_name=user.full_name, role=user.role)
    db.add(db_user)
    db.commit()
    db.refresh(db_user)
    return db_user

# 2. Login
@app.post("/auth/login", response_model=Token)
def login(form_data: OAuth2PasswordRequestForm = Depends(), db: Session = Depends(get_db)):
    user = authenticate_user(db, form_data.username, form_data.password)
    if not user:
        raise HTTPException(status_code=400, detail="Username atau password salah")
    access_token = create_access_token(data={"sub": user.username}, expires_delta=timedelta(minutes=ACCESS_TOKEN_EXPIRE_MINUTES))
    return {"access_token": access_token, "token_type": "bearer"}

# 3. GET /users (hanya petugas)
@app.get("/users", response_model=List[UserOut])
def get_all_users(db: Session = Depends(get_db), current_user: UserModel = Depends(get_current_petugas)):
    return db.query(UserModel).all()

# 4. GET /users/{id}
@app.get("/users/{user_id}", response_model=UserOut)
def get_user(user_id: int, db: Session = Depends(get_db), current_user: UserModel = Depends(get_current_user)):
    user = db.query(UserModel).filter(UserModel.id == user_id).first()
    if not user:
        raise HTTPException(status_code=404, detail="User tidak ditemukan")
    if current_user.role != "petugas" and current_user.id != user_id:
        raise HTTPException(status_code=403, detail="Tidak diizinkan")
    return user

# 5. PUT /users/{id}
@app.put("/users/{user_id}", response_model=UserOut)
def update_user(user_id: int, user_update: UserUpdate, db: Session = Depends(get_db), current_user: UserModel = Depends(get_current_user)):
    user = db.query(UserModel).filter(UserModel.id == user_id).first()
    if not user:
        raise HTTPException(status_code=404, detail="User tidak ditemukan")
    if current_user.role != "petugas" and current_user.id != user_id:
        raise HTTPException(status_code=403, detail="Tidak diizinkan")
    for key, value in user_update.dict(exclude_unset=True).items():
        if key == "role" and current_user.role != "petugas":
            continue
        setattr(user, key, value)
    db.commit()
    db.refresh(user)
    return user

# 6. DELETE /users/{id} (hanya petugas)
@app.delete("/users/{user_id}")
def delete_user(user_id: int, db: Session = Depends(get_db), current_user: UserModel = Depends(get_current_petugas)):
    user = db.query(UserModel).filter(UserModel.id == user_id).first()
    if not user:
        raise HTTPException(status_code=404, detail="User tidak ditemukan")
    db.delete(user)
    db.commit()
    return {"message": "User dihapus"}

# 7. POST /buku (hanya petugas)
@app.post("/buku", response_model=BukuOut, status_code=201)
def create_buku(buku: BukuCreate, db: Session = Depends(get_db), current_user: UserModel = Depends(get_current_petugas)):
    db_buku = BukuModel(**buku.dict())
    db.add(db_buku)
    db.commit()
    db.refresh(db_buku)
    return db_buku

# 8. GET /buku (semua user)
@app.get("/buku", response_model=List[BukuOut])
def get_all_buku(db: Session = Depends(get_db), current_user: UserModel = Depends(get_current_user)):
    return db.query(BukuModel).all()

# 9. GET /buku/{id}
@app.get("/buku/{buku_id}", response_model=BukuOut)
def get_buku(buku_id: int, db: Session = Depends(get_db), current_user: UserModel = Depends(get_current_user)):
    buku = db.query(BukuModel).filter(BukuModel.id == buku_id).first()
    if not buku:
        raise HTTPException(status_code=404, detail="Buku tidak ditemukan")
    return buku

# 10. PUT /buku/{id} (hanya petugas)
@app.put("/buku/{buku_id}", response_model=BukuOut)
def update_buku(buku_id: int, buku_update: BukuUpdate, db: Session = Depends(get_db), current_user: UserModel = Depends(get_current_petugas)):
    buku = db.query(BukuModel).filter(BukuModel.id == buku_id).first()
    if not buku:
        raise HTTPException(status_code=404, detail="Buku tidak ditemukan")
    for key, value in buku_update.dict(exclude_unset=True).items():
        setattr(buku, key, value)
    db.commit()
    db.refresh(buku)
    return buku

# 11. DELETE /buku/{id} (hanya petugas)
@app.delete("/buku/{buku_id}")
def delete_buku(buku_id: int, db: Session = Depends(get_db), current_user: UserModel = Depends(get_current_petugas)):
    buku = db.query(BukuModel).filter(BukuModel.id == buku_id).first()
    if not buku:
        raise HTTPException(status_code=404, detail="Buku tidak ditemukan")
    db.delete(buku)
    db.commit()
    return {"message": "Buku dihapus"}

# 12. POST /peminjaman
@app.post("/peminjaman", response_model=PeminjamanOut, status_code=201)
def create_peminjaman(pinjam: PeminjamanCreate, db: Session = Depends(get_db), current_user: UserModel = Depends(get_current_user)):
    user = db.query(UserModel).filter(UserModel.id == pinjam.user_id).first()
    if not user:
        raise HTTPException(status_code=404, detail="User tidak ditemukan")
    if current_user.role != "petugas" and current_user.id != pinjam.user_id:
        raise HTTPException(status_code=403, detail="Hanya bisa meminjam untuk diri sendiri")
    buku = db.query(BukuModel).filter(BukuModel.id == pinjam.buku_id).first()
    if not buku or buku.stok <= 0:
        raise HTTPException(status_code=400, detail="Buku tidak tersedia")
    buku.stok -= 1
    db_pinjam = PeminjamanModel(user_id=pinjam.user_id, buku_id=pinjam.buku_id)
    db.add(db_pinjam)
    db.commit()
    db.refresh(db_pinjam)
    return db_pinjam

# 13. GET /peminjaman
@app.get("/peminjaman", response_model=List[PeminjamanOut])
def get_all_peminjaman(db: Session = Depends(get_db), current_user: UserModel = Depends(get_current_user)):
    if current_user.role == "petugas":
        return db.query(PeminjamanModel).all()
    else:
        return db.query(PeminjamanModel).filter(PeminjamanModel.user_id == current_user.id).all()

# 14. GET /peminjaman/{id}
@app.get("/peminjaman/{pinjam_id}", response_model=PeminjamanOut)
def get_peminjaman(pinjam_id: int, db: Session = Depends(get_db), current_user: UserModel = Depends(get_current_user)):
    pinjam = db.query(PeminjamanModel).filter(PeminjamanModel.id == pinjam_id).first()
    if not pinjam:
        raise HTTPException(status_code=404, detail="Peminjaman tidak ditemukan")
    if current_user.role != "petugas" and pinjam.user_id != current_user.id:
        raise HTTPException(status_code=403, detail="Tidak diizinkan")
    return pinjam

# 15. PUT /peminjaman/{id} (perpanjang)
@app.put("/peminjaman/{pinjam_id}", response_model=PeminjamanOut)
def perpanjang_peminjaman(pinjam_id: int, update_data: PeminjamanUpdate, db: Session = Depends(get_db), current_user: UserModel = Depends(get_current_user)):
    pinjam = db.query(PeminjamanModel).filter(PeminjamanModel.id == pinjam_id).first()
    if not pinjam:
        raise HTTPException(status_code=404, detail="Peminjaman tidak ditemukan")
    if current_user.role != "petugas" and pinjam.user_id != current_user.id:
        raise HTTPException(status_code=403, detail="Tidak diizinkan")
    if pinjam.status != "dipinjam":
        raise HTTPException(status_code=400, detail="Hanya bisa perpanjang yang masih dipinjam")
    if update_data.tgl_jatuh_tempo:
        pinjam.tgl_jatuh_tempo = update_data.tgl_jatuh_tempo
    else:
        pinjam.tgl_jatuh_tempo += timedelta(days=7)
    db.commit()
    db.refresh(pinjam)
    return pinjam

# 16. DELETE /peminjaman/{id} (batalkan)
@app.delete("/peminjaman/{pinjam_id}")
def batalkan_peminjaman(pinjam_id: int, db: Session = Depends(get_db), current_user: UserModel = Depends(get_current_user)):
    pinjam = db.query(PeminjamanModel).filter(PeminjamanModel.id == pinjam_id).first()
    if not pinjam:
        raise HTTPException(status_code=404, detail="Peminjaman tidak ditemukan")
    if current_user.role != "petugas" and pinjam.user_id != current_user.id:
        raise HTTPException(status_code=403, detail="Tidak diizinkan")
    if pinjam.status != "dipinjam":
        raise HTTPException(status_code=400, detail="Hanya bisa batalkan yang masih dipinjam")
    buku = db.query(BukuModel).filter(BukuModel.id == pinjam.buku_id).first()
    if buku:
        buku.stok += 1
    db.delete(pinjam)
    db.commit()
    return {"message": "Peminjaman dibatalkan"}

# 17. POST /pengembalian
@app.post("/pengembalian")
def proses_pengembalian(req: PengembalianRequest, db: Session = Depends(get_db), current_user: UserModel = Depends(get_current_user)):
    pinjam = db.query(PeminjamanModel).filter(PeminjamanModel.id == req.peminjaman_id).first()
    if not pinjam:
        raise HTTPException(status_code=404, detail="Peminjaman tidak ditemukan")
    if current_user.role != "petugas" and pinjam.user_id != current_user.id:
        raise HTTPException(status_code=403, detail="Tidak diizinkan")
    if pinjam.status != "dipinjam":
        raise HTTPException(status_code=400, detail="Peminjaman sudah dikembalikan")
    pinjam.tgl_kembali = datetime.now()
    pinjam.status = "kembali"
    buku = db.query(BukuModel).filter(BukuModel.id == pinjam.buku_id).first()
    if buku:
        buku.stok += 1
    denda = 0
    if pinjam.tgl_kembali > pinjam.tgl_jatuh_tempo:
        terlambat_hari = (pinjam.tgl_kembali - pinjam.tgl_jatuh_tempo).days
        denda = terlambat_hari * 1000
        denda_obj = DendaModel(peminjaman_id=pinjam.id, user_id=pinjam.user_id, jumlah=denda, dibayar=False)
        db.add(denda_obj)
    db.commit()
    return {"message": "Pengembalian berhasil", "denda": denda}

# 18. GET /denda
@app.get("/denda", response_model=List[DendaOut])
def get_all_denda(db: Session = Depends(get_db), current_user: UserModel = Depends(get_current_user)):
    if current_user.role == "petugas":
        return db.query(DendaModel).all()
    else:
        return db.query(DendaModel).filter(DendaModel.user_id == current_user.id).all()

# 19. GET /denda/{id}
@app.get("/denda/{denda_id}", response_model=DendaOut)
def get_denda(denda_id: int, db: Session = Depends(get_db), current_user: UserModel = Depends(get_current_user)):
    denda = db.query(DendaModel).filter(DendaModel.id == denda_id).first()
    if not denda:
        raise HTTPException(status_code=404, detail="Denda tidak ditemukan")
    if current_user.role != "petugas" and denda.user_id != current_user.id:
        raise HTTPException(status_code=403, detail="Tidak diizinkan")
    return denda

# 20. PUT /denda/{id} (bayar denda)
@app.put("/denda/{denda_id}", response_model=DendaOut)
def update_denda(denda_id: int, denda_update: DendaUpdate, db: Session = Depends(get_db), current_user: UserModel = Depends(get_current_user)):
    denda = db.query(DendaModel).filter(DendaModel.id == denda_id).first()
    if not denda:
        raise HTTPException(status_code=404, detail="Denda tidak ditemukan")
    if current_user.role != "petugas" and denda.user_id != current_user.id:
        raise HTTPException(status_code=403, detail="Tidak diizinkan")
    denda.dibayar = denda_update.dibayar
    db.commit()
    db.refresh(denda)
    return denda

# 21. DELETE /denda/{id} (hanya petugas)
@app.delete("/denda/{denda_id}")
def delete_denda(denda_id: int, db: Session = Depends(get_db), current_user: UserModel = Depends(get_current_petugas)):
    denda = db.query(DendaModel).filter(DendaModel.id == denda_id).first()
    if not denda:
        raise HTTPException(status_code=404, detail="Denda tidak ditemukan")
    db.delete(denda)
    db.commit()
    return {"message": "Denda dihapus"}

# 22. Laporan peminjaman (hanya petugas)
@app.get("/laporan/peminjaman")
def laporan_peminjaman(user_id: Optional[int] = None, start_date: Optional[datetime] = None, end_date: Optional[datetime] = None, db: Session = Depends(get_db), current_user: UserModel = Depends(get_current_petugas)):
    query = db.query(PeminjamanModel)
    if user_id:
        query = query.filter(PeminjamanModel.user_id == user_id)
    if start_date:
        query = query.filter(PeminjamanModel.tgl_pinjam >= start_date)
    if end_date:
        query = query.filter(PeminjamanModel.tgl_pinjam <= end_date)
    return query.all()

# 23. Laporan denda (hanya petugas)
@app.get("/laporan/denda")
def laporan_denda(user_id: Optional[int] = None, dibayar: Optional[bool] = None, db: Session = Depends(get_db), current_user: UserModel = Depends(get_current_petugas)):
    query = db.query(DendaModel)
    if user_id:
        query = query.filter(DendaModel.user_id == user_id)
    if dibayar is not None:
        query = query.filter(DendaModel.dibayar == dibayar)
    return query.all()
```

## Cara Menjalankan

1. Simpan kode di atas sebagai `main.py`.
2. Buka terminal di folder tersebut.
3. Install dependensi jika belum:
   ```bash
   pip install fastapi uvicorn sqlalchemy python-jose[cryptography] passlib[bcrypt] python-multipart
   ```
4. Jalankan server:
   ```bash
   uvicorn main:app --reload
   ```
5. Buka `http://localhost:8000/docs` untuk melihat dokumentasi interaktif.

Data dummy akan otomatis tergenerate saat pertama kali server berjalan (jika database kosong). Anda akan mendapatkan:
- 2 petugas (`petugas1`/`admin123`, `petugas2`/`admin123`)
- 20 anggota (`anggota1`/`rahasia` hingga `anggota20`/`rahasia`)
- 24 buku dengan stok bervariasi
- 30 peminjaman dengan berbagai status
- Minimal 20 denda


---

## 🗃️ Data Dummy yang Dihasilkan Seeder

Setelah server pertama kali dijalankan, secara otomatis akan terisi:

| Tabel       | Jumlah Record | Keterangan                                                                 |
|-------------|---------------|-----------------------------------------------------------------------------|
| **users**   | 22            | 2 petugas + 20 anggota (username anggota1..20, password: `rahasia`)        |
| **buku**    | 24            | Judul bervariasi, stok 1-5, tahun 2015-2023                                 |
| **peminjaman** | 30         | Beberapa status dipinjam, kembali, terlambat. Sudah ada tgl_pinjam acak.    |
| **denda**   | 20+           | Denda dari peminjaman terlambat (Rp 1000/hari), sebagian sudah dibayar.     |

**Akses awal**:
- Petugas: `petugas1` / `admin123` atau `petugas2` / `admin123`
- Anggota: `anggota1` / `rahasia` (dan seterusnya)

---

## 🧪 Skenario Pengujian Postman (25 Test Case)

Buat **Collection** `Perpustakaan API` dan **Environment** dengan variabel:

| Variable          | Initial Value             |
|-------------------|---------------------------|
| base_url          | http://localhost:8000     |
| token_petugas     | (kosong)                  |
| token_anggota     | (kosong)                  |
| user_id_anggota   | (isi dari GET /users nanti)|
| buku_id           | (isi dari GET /buku)      |
| pinjam_id         | (isi dari POST /peminjaman)|
| denda_id          | (isi dari GET /denda)     |

### Daftar Test Case (Lengkap dengan Assertion)

| No | Nama Test Case                     | Endpoint & Method          | Skenario & Data                                                                 | Expected Result & Assertion                                                                 |
|----|------------------------------------|----------------------------|---------------------------------------------------------------------------------|---------------------------------------------------------------------------------------------|
| 1  | Register Anggota Baru              | POST /auth/register        | Body: `{"username":"test001","email":"test@mail.com","password":"123","full_name":"Test User","role":"anggota"}` | Status 201, response memiliki `id`, `role="anggota"`                                        |
| 2  | Register Gagal Duplikat Username   | POST /auth/register        | Username yang sudah ada (misal `anggota1`)                                      | Status 400, pesan "Username atau email sudah terdaftar"                                     |
| 3  | Login Petugas                      | POST /auth/login           | Form data: `username=petugas1&password=admin123`                                | Status 200, dapat `access_token`, simpan ke environment `token_petugas`                    |
| 4  | Login Anggota                      | POST /auth/login           | Form data: `username=anggota1&password=rahasia`                                 | Status 200, dapat token, simpan `token_anggota`                                             |
| 5  | Get All Users (petugas)            | GET /users                 | Header: `Authorization: Bearer {{token_petugas}}`                               | Status 200, array panjangnya >= 22, salah satu user memiliki role petugas                  |
| 6  | Get All Users (anggota) - Gagal    | GET /users                 | Header: `Bearer {{token_anggota}}`                                              | Status 403, karena anggota tidak boleh list semua user                                      |
| 7  | Get User Sendiri (anggota)         | GET /users/{{user_id_anggota}} | Pakai token anggota, user_id milik sendiri                                    | Status 200, data username sesuai                                                           |
| 8  | Update Profil Anggota              | PUT /users/{{user_id_anggota}} | Body: `{"full_name":"Nama Baru"}` dengan token anggota                         | Status 200, full_name berubah                                                               |
| 9  | Create Buku (petugas)              | POST /buku                 | Body valid, token petugas                                                      | Status 201, response memiliki id, simpan `buku_id`                                          |
| 10 | Create Buku (anggota) - Gagal      | POST /buku                 | Token anggota                                                                  | Status 403                                                                                  |
| 11 | Get All Buku (anggota)             | GET /buku                  | Token anggota                                                                  | Status 200, list minimal 24 buku                                                           |
| 12 | Update Buku (petugas)              | PUT /buku/{{buku_id}}      | Body: `{"judul":"Judul Baru"}` token petugas                                   | Status 200, judul berubah                                                                   |
| 13 | Delete Buku (petugas)              | DELETE /buku/{{buku_id}}   | Token petugas                                                                  | Status 200, message "Buku dihapus"                                                          |
| 14 | Pinjam Buku (anggota untuk diri)   | POST /peminjaman           | Body: `{"user_id":{{user_id_anggota}},"buku_id":<buku_dengan_stok>}` token anggota | Status 201, data peminjaman, stok buku berkurang, simpan `pinjam_id`                        |
| 15 | Pinjam Buku Gagal (stok 0)         | POST /peminjaman           | Buku dengan stok 0 (bisa cari dari GET /buku)                                  | Status 400, "Buku tidak tersedia"                                                           |
| 16 | Get Peminjaman Milik Anggota       | GET /peminjaman            | Token anggota                                                                  | Status 200, semua peminjaman memiliki user_id = current user.id                             |
| 17 | Perpanjang Peminjaman              | PUT /peminjaman/{{pinjam_id}} | Token anggota, tanpa body (default +7 hari)                                   | Status 200, `tgl_jatuh_tempo` bertambah 7 hari                                              |
| 18 | Batalkan Peminjaman                | DELETE /peminjaman/{{pinjam_id}} | Token anggota (asumsi masih dipinjam)                                        | Status 200, message sukses, stok buku kembali                                                 |
| 19 | Pengembalian Tepat Waktu           | (Buat peminjaman baru lalu langsung kembalikan) POST /pengembalian | Body: `{"peminjaman_id":<id_baru>}` token anggota | Status 200, `"denda":0`                                                                     |
| 20 | Pengembalian Terlambat             | (Pinjam buku lalu ubah tgl_jatuh_tempo via SQL atau endpoint admin) lalu kembalikan | Body sama, token anggota | Status 200, `"denda" > 0`, dan denda tersimpan di tabel denda                               |
| 21 | Get Denda (anggota)                | GET /denda                 | Token anggota                                                                  | Status 200, list denda miliknya (bisa kosong)                                               |
| 22 | Bayar Denda                        | PUT /denda/{{denda_id}}    | Body: `{"dibayar": true}` token anggota                                        | Status 200, `dibayar` menjadi true                                                          |
| 23 | Laporan Peminjaman (petugas)       | GET /laporan/peminjaman?user_id={{user_id_anggota}} | Token petugas                               | Status 200, array peminjaman sesuai filter user_id                                           |
| 24 | Laporan Denda (petugas)            | GET /laporan/denda?dibayar=false | Token petugas                                                          | Status 200, semua denda yang belum dibayar                                                   |
| 25 | Delete User (petugas)              | DELETE /users/{{user_id_anggota}} | Token petugas                                                          | Status 200, user terhapus. Coba login dengan user tersebut -> 400                           |

---

## 📥 Contoh Test Script di Postman (untuk Test Case #1)

**Pre-request Script**: (kosong)

**Request Body** (raw JSON):
```json
{
    "username": "test001",
    "email": "test001@mail.com",
    "password": "123456",
    "full_name": "Test User",
    "role": "anggota"
}
```

**Tests**:
```javascript
pm.test("Status code is 201", function () {
    pm.response.to.have.status(201);
});
pm.test("Response contains id", function () {
    var jsonData = pm.response.json();
    pm.expect(jsonData).to.have.property("id");
    pm.environment.set("user_id_baru", jsonData.id);
});
pm.test("Role is anggota", function () {
    var jsonData = pm.response.json();
    pm.expect(jsonData.role).to.eql("anggota");
});
```

---

## 🧩 Cara Menjalankan Collection Runner

1. Impor environment dan collection ke Postman.
2. Pastikan server FastAPI berjalan.
3. Buka **Collection Runner**, pilih collection `Perpustakaan API`.
4. Atur **iterations** = 1, **delay** = 500ms.
5. Klik **Run**.
6. Amati hasil: semua test case harus berwarna hijau (passed).

---
