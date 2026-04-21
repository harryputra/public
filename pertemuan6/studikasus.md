# Studi Kasus API Testing dengan Postman: Sistem Perpustakaan (FastAPI)

## 📘 Deskripsi Studi Kasus

Anda diminta untuk menguji sebuah **API Sistem Perpustakaan** yang dibangun menggunakan **FastAPI (Python)**. API ini menyediakan layanan manajemen anggota, petugas, buku, peminjaman, pengembalian, dan denda. Sebagai seorang QA Engineer, Anda harus membuat skenario pengujian yang komprehensif menggunakan **Postman**, mencakup **minimal 20 endpoint** dengan berbagai method HTTP (GET, POST, PUT, DELETE) serta payload JSON. Pastikan Anda menguji skenario positif (berhasil) dan negatif (gagal) sesuai dengan **20+ test case** yang telah ditentukan.

### Tujuan Pengujian
- Memverifikasi fungsionalitas CRUD pada semua entitas.
- Memastikan autentikasi dan otorisasi berjalan dengan benar (JWT token).
- Menguji alur bisnis: registrasi → login → tambah buku → pinjam buku → kembali → hitung denda.
- Menemukan potensi bug melalui skenario negatif (data tidak valid, akses tanpa token, dll.).

---

## 🛠️ Persiapan Lingkungan

### 1. Prasyarat
- Python 3.9+ terinstal.
- Postman (versi terbaru).
- (Opsional) Git untuk clone source code.

### 2. Instalasi dan Menjalankan Server
```bash
# Clone atau buat folder proyek
mkdir perpustakaan-api
cd perpustakaan-api

# Buat virtual environment
python -m venv venv
source venv/bin/activate  # Linux/Mac
venv\Scripts\activate     # Windows

# Install dependensi
pip install fastapi uvicorn sqlalchemy python-jose[cryptography] passlib[bcrypt] python-multipart

# Simpan source code ke file main.py (lihat bagian Source Code)
# Jalankan server
uvicorn main:app --reload --port 8000
```

Server akan berjalan di `http://localhost:8000`. Dokumentasi otomatis tersedia di `http://localhost:8000/docs`.

---

## 📚 Dokumentasi Endpoint (20+ endpoint)

| No | Method | Endpoint                   | Deskripsi                         | Auth |
|----|--------|----------------------------|-----------------------------------|------|
| 1  | POST   | `/auth/register`           | Registrasi user baru              | Tidak |
| 2  | POST   | `/auth/login`              | Login, dapatkan token             | Tidak |
| 3  | GET    | `/users`                   | Daftar semua user                 | Token |
| 4  | GET    | `/users/{id}`              | Detail user                       | Token |
| 5  | PUT    | `/users/{id}`              | Update user                       | Token |
| 6  | DELETE | `/users/{id}`              | Hapus user                        | Token |
| 7  | POST   | `/buku`                    | Tambah buku baru                  | Token |
| 8  | GET    | `/buku`                    | Daftar semua buku                 | Token |
| 9  | GET    | `/buku/{id}`               | Detail buku                       | Token |
| 10 | PUT    | `/buku/{id}`               | Update buku                       | Token |
| 11 | DELETE | `/buku/{id}`               | Hapus buku                        | Token |
| 12 | POST   | `/peminjaman`              | Buat peminjaman baru              | Token |
| 13 | GET    | `/peminjaman`              | Daftar peminjaman                 | Token |
| 14 | GET    | `/peminjaman/{id}`         | Detail peminjaman                 | Token |
| 15 | PUT    | `/peminjaman/{id}`         | Perpanjang peminjaman             | Token |
| 16 | DELETE | `/peminjaman/{id}`         | Batalkan peminjaman               | Token |
| 17 | POST   | `/pengembalian`            | Proses pengembalian & hitung denda| Token |
| 18 | GET    | `/denda`                   | Daftar denda                      | Token |
| 19 | GET    | `/denda/{id}`              | Detail denda                      | Token |
| 20 | PUT    | `/denda/{id}`              | Update denda (misal sudah bayar)  | Token |
| 21 | DELETE | `/denda/{id}`              | Hapus denda                       | Token |
| 22 | GET    | `/laporan/peminjaman`      | Laporan peminjaman (filter)       | Token |
| 23 | GET    | `/laporan/denda`           | Laporan denda (filter)            | Token |

> **Catatan Auth**: Untuk endpoint yang memerlukan token, sertakan header `Authorization: Bearer <token>`.

---

## 💻 Source Code Lengkap (FastAPI)

Simpan kode berikut sebagai `main.py`.

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
import os

# ---------- Konfigurasi ----------
SECRET_KEY = "supersecretkeyforjwt"
ALGORITHM = "HS256"
ACCESS_TOKEN_EXPIRE_MINUTES = 30

# Database SQLite
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
    role = Column(String, default="anggota")  # "anggota" atau "petugas"
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
    status = Column(String, default="dipinjam")  # dipinjam, kembali, terlambat

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

# ---------- FastAPI App ----------
app = FastAPI(title="Sistem Perpustakaan API", version="1.0")

# Dependency untuk database
def get_db():
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()

# ---------- Endpoints ----------
# 1. Registrasi
@app.post("/auth/register", response_model=UserOut, status_code=201)
def register(user: UserCreate, db: Session = Depends(get_db)):
    existing = db.query(UserModel).filter((UserModel.username == user.username) | (UserModel.email == user.email)).first()
    if existing:
        raise HTTPException(status_code=400, detail="Username atau email sudah terdaftar")
    hashed = get_password_hash(user.password)
    db_user = UserModel(
        username=user.username,
        email=user.email,
        hashed_password=hashed,
        full_name=user.full_name,
        role=user.role
    )
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

# 3. GET /users
@app.get("/users", response_model=List[UserOut])
def get_all_users(db: Session = Depends(get_db), current_user: UserModel = Depends(get_current_petugas)):
    return db.query(UserModel).all()

# 4. GET /users/{id}
@app.get("/users/{user_id}", response_model=UserOut)
def get_user(user_id: int, db: Session = Depends(get_db), current_user: UserModel = Depends(get_current_user)):
    user = db.query(UserModel).filter(UserModel.id == user_id).first()
    if not user:
        raise HTTPException(status_code=404, detail="User tidak ditemukan")
    # Anggota hanya bisa melihat dirinya sendiri, petugas bisa semua
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
    if user_update.full_name is not None:
        user.full_name = user_update.full_name
    if user_update.email is not None:
        user.email = user_update.email
    if user_update.role is not None and current_user.role == "petugas":
        user.role = user_update.role
    if user_update.is_active is not None and current_user.role == "petugas":
        user.is_active = user_update.is_active
    db.commit()
    db.refresh(user)
    return user

# 6. DELETE /users/{id}
@app.delete("/users/{user_id}")
def delete_user(user_id: int, db: Session = Depends(get_db), current_user: UserModel = Depends(get_current_petugas)):
    user = db.query(UserModel).filter(UserModel.id == user_id).first()
    if not user:
        raise HTTPException(status_code=404, detail="User tidak ditemukan")
    db.delete(user)
    db.commit()
    return {"message": "User dihapus"}

# 7. POST /buku
@app.post("/buku", response_model=BukuOut, status_code=201)
def create_buku(buku: BukuCreate, db: Session = Depends(get_db), current_user: UserModel = Depends(get_current_petugas)):
    db_buku = BukuModel(**buku.dict())
    db.add(db_buku)
    db.commit()
    db.refresh(db_buku)
    return db_buku

# 8. GET /buku
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

# 10. PUT /buku/{id}
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

# 11. DELETE /buku/{id}
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
    # Cek user dan buku
    user = db.query(UserModel).filter(UserModel.id == pinjam.user_id).first()
    if not user:
        raise HTTPException(status_code=404, detail="User tidak ditemukan")
    if current_user.role != "petugas" and current_user.id != pinjam.user_id:
        raise HTTPException(status_code=403, detail="Hanya bisa meminjam untuk diri sendiri")
    buku = db.query(BukuModel).filter(BukuModel.id == pinjam.buku_id).first()
    if not buku or buku.stok <= 0:
        raise HTTPException(status_code=400, detail="Buku tidak tersedia")
    # Kurangi stok
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

# 16. DELETE /peminjaman/{id} (batal)
@app.delete("/peminjaman/{pinjam_id}")
def batalkan_peminjaman(pinjam_id: int, db: Session = Depends(get_db), current_user: UserModel = Depends(get_current_user)):
    pinjam = db.query(PeminjamanModel).filter(PeminjamanModel.id == pinjam_id).first()
    if not pinjam:
        raise HTTPException(status_code=404, detail="Peminjaman tidak ditemukan")
    if current_user.role != "petugas" and pinjam.user_id != current_user.id:
        raise HTTPException(status_code=403, detail="Tidak diizinkan")
    if pinjam.status != "dipinjam":
        raise HTTPException(status_code=400, detail="Hanya bisa batalkan yang masih dipinjam")
    # Kembalikan stok buku
    buku = db.query(BukuModel).filter(BukuModel.id == pinjam.buku_id).first()
    if buku:
        buku.stok += 1
    db.delete(pinjam)
    db.commit()
    return {"message": "Peminjaman dibatalkan"}

# 17. POST /pengembalian (proses kembali dan hitung denda)
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
    # Kembalikan stok buku
    buku = db.query(BukuModel).filter(BukuModel.id == pinjam.buku_id).first()
    if buku:
        buku.stok += 1
    # Hitung denda jika terlambat
    denda = 0
    if pinjam.tgl_kembali > pinjam.tgl_jatuh_tempo:
        terlambat_hari = (pinjam.tgl_kembali - pinjam.tgl_jatuh_tempo).days
        denda = terlambat_hari * 1000  # Rp 1000 per hari
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

# 20. PUT /denda/{id} (update pembayaran)
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

# 21. DELETE /denda/{id}
@app.delete("/denda/{denda_id}")
def delete_denda(denda_id: int, db: Session = Depends(get_db), current_user: UserModel = Depends(get_current_petugas)):
    denda = db.query(DendaModel).filter(DendaModel.id == denda_id).first()
    if not denda:
        raise HTTPException(status_code=404, detail="Denda tidak ditemukan")
    db.delete(denda)
    db.commit()
    return {"message": "Denda dihapus"}

# 22. Laporan peminjaman (filter by user_id dan tanggal)
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

# 23. Laporan denda
@app.get("/laporan/denda")
def laporan_denda(user_id: Optional[int] = None, dibayar: Optional[bool] = None, db: Session = Depends(get_db), current_user: UserModel = Depends(get_current_petugas)):
    query = db.query(DendaModel)
    if user_id:
        query = query.filter(DendaModel.user_id == user_id)
    if dibayar is not None:
        query = query.filter(DendaModel.dibayar == dibayar)
    return query.all()
```

> **Catatan**: Untuk keperluan testing, Anda dapat mengisi data awal (seed) secara manual melalui endpoint atau menggunakan Postman.

---

## 🧪 Skenario Pengujian Postman (20+ Test Case)

Buat **Collection** di Postman dengan nama `Perpustakaan API Tests`. Tambahkan **Environment** untuk menyimpan variabel seperti `base_url`, `token`, `user_id`, `buku_id`, `pinjam_id`, `denda_id`.

### Environment Variables (contoh)
| Variable      | Initial Value          |
|---------------|------------------------|
| base_url      | http://localhost:8000  |
| token         | (kosong)               |
| user_id_anggota| (isi setelah register) |
| user_id_petugas| (isi setelah register) |
| buku_id       | (isi setelah create)   |
| pinjam_id     | (isi setelah create)   |
| denda_id      | (isi setelah kembali)  |

### Daftar Test Case (Minimal 20)

| No | Nama Test Case                         | Endpoint & Method          | Skenario                                                                 | Expected Result                                                                 |
|----|----------------------------------------|----------------------------|--------------------------------------------------------------------------|---------------------------------------------------------------------------------|
| 1  | Register Anggota Baru                  | POST /auth/register        | Kirim data user baru dengan role "anggota"                               | Status 201, response mengandung id, username, role=anggota                    |
| 2  | Register Gagal (duplikat username)     | POST /auth/register        | Kirim username yang sudah terdaftar                                      | Status 400, pesan error "Username atau email sudah terdaftar"                  |
| 3  | Login Anggota                          | POST /auth/login           | Kirim username & password yang benar (form data)                         | Status 200, dapat token, simpan ke environment                                 |
| 4  | Login Gagal (password salah)           | POST /auth/login           | Password salah                                                           | Status 400, detail "Username atau password salah"                              |
| 5  | Akses /users tanpa token               | GET /users                 | Tidak menyertakan header Authorization                                   | Status 401, detail "Not authenticated"                                         |
| 6  | Akses /users dengan token anggota      | GET /users                 | Sertakan token anggota (bukan petugas)                                   | Status 403, karena hanya petugas yang bisa list semua user                     |
| 7  | Get user sendiri (anggota)             | GET /users/{id}            | Anggota melihat datanya sendiri                                          | Status 200, data sesuai                                                        |
| 8  | Update profil anggota sendiri          | PUT /users/{id}            | Ganti full_name                                                          | Status 200, full_name berubah                                                  |
| 9  | Register Petugas                       | POST /auth/register        | role="petugas"                                                           | Status 201, role petugas                                                       |
| 10 | Login Petugas                          | POST /auth/login           | Login dengan akun petugas                                                | Status 200, token petugas                                                      |
| 11 | Create Buku (oleh petugas)             | POST /buku                 | Dengan token petugas, kirim data buku valid                              | Status 201, buku tersimpan, simpan buku_id                                     |
| 12 | Create Buku gagal (non-petugas)        | POST /buku                 | Gunakan token anggota                                                    | Status 403                                                                     |
| 13 | Get all buku (anggota)                 | GET /buku                  | Token anggota                                                            | Status 200, list buku                                                          |
| 14 | Update Buku (petugas)                  | PUT /buku/{id}             | Ubah judul buku                                                          | Status 200, judul berubah                                                      |
| 15 | Delete Buku (petugas)                  | DELETE /buku/{id}          | Hapus buku yang sudah ada                                                | Status 200, message "Buku dihapus"                                             |
| 16 | Pinjam Buku (anggota untuk diri sendiri)| POST /peminjaman           | user_id = id anggota, buku_id valid, stok >0                            | Status 201, data peminjaman, stok berkurang, simpan pinjam_id                 |
| 17 | Pinjam Buku gagal (stok habis)         | POST /peminjaman           | Buku dengan stok 0                                                       | Status 400, "Buku tidak tersedia"                                              |
| 18 | Get list peminjaman (anggota)          | GET /peminjaman            | Token anggota                                                            | Status 200, hanya menampilkan peminjaman miliknya                              |
| 19 | Perpanjang peminjaman                  | PUT /peminjaman/{id}       | Kirim tgl_jatuh_tempo baru atau tanpa body                               | Status 200, tanggal jatuh tempo bertambah 7 hari                              |
| 20 | Pengembalian buku tepat waktu          | POST /pengembalian         | Kembalikan buku sebelum jatuh tempo                                      | Status 200, message sukses, denda=0, stok buku kembali                        |
| 21 | Pengembalian terlambat (denda)         | (persiapan: pinjam lalu ubah tgl_jatuh_tempo ke masa lalu) | Kembalikan setelah jatuh tempo | Status 200, denda >0, data denda tersimpan, simpan denda_id                   |
| 22 | Get denda milik anggota                | GET /denda                 | Token anggota                                                            | Status 200, denda muncul                                                       |
| 23 | Bayar denda (update dibayar=true)      | PUT /denda/{id}            | Set dibayar: true                                                        | Status 200, dibayar menjadi true                                               |
| 24 | Laporan peminjaman (petugas)           | GET /laporan/peminjaman?user_id=... | Token petugas, filter user_id | Status 200, daftar peminjaman sesuai filter                                   |
| 25 | Delete user oleh petugas               | DELETE /users/{id}         | Hapus anggota yang sudah tidak aktif                                     | Status 200, user terhapus                                                      |

> **Catatan**: Test case di atas sudah lebih dari 20. Anda dapat menjalankan secara berurutan dengan bantuan **Collection Runner** Postman atau menggunakan **Pre-request Script** untuk mengatur dependensi data.

### Contoh Pre-request Script untuk Login & Set Token
Pada folder/collection, tambahkan script berikut di tab **Pre-request Script** (jika perlu otomatis login):
```javascript
// Hanya jika belum ada token atau token expired
if (!pm.environment.get("token")) {
    pm.sendRequest({
        url: pm.environment.get("base_url") + "/auth/login",
        method: 'POST',
        header: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: {
            mode: 'urlencoded',
            urlencoded: [
                {key: "username", value: "test_anggota"},
                {key: "password", value: "rahasia"}
            ]
        }
    }, function (err, res) {
        if (err) console.log(err);
        else {
            pm.environment.set("token", res.json().access_token);
        }
    });
}
```

### Contoh Test Script pada Request (menggunakan Chai assertion)
Misal pada **Register Anggota**:
```javascript
pm.test("Status code is 201", function () {
    pm.response.to.have.status(201);
});
pm.test("Response has user id", function () {
    var jsonData = pm.response.json();
    pm.expect(jsonData).to.have.property("id");
    pm.environment.set("user_id_anggota", jsonData.id);
});
pm.test("Role is anggota", function () {
    var jsonData = pm.response.json();
    pm.expect(jsonData.role).to.eql("anggota");
});
```

---

## 📥 Contoh Export Postman Collection (Cuplikan)

Anda dapat mengimpor file JSON berikut ke Postman. Karena panjang, saya akan berikan struktur singkat. Buat sendiri di Postman mengikuti endpoint di atas, atau gunakan generator.

```json
{
  "info": {
    "name": "Perpustakaan API Tests",
    "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
  },
  "item": [
    {
      "name": "Auth",
      "item": [
        {
          "name": "Register Anggota",
          "request": {
            "method": "POST",
            "header": [],
            "body": {
              "mode": "raw",
              "raw": "{\n    \"username\": \"anggota1\",\n    \"email\": \"anggota1@mail.com\",\n    \"password\": \"rahasia\",\n    \"full_name\": \"Anggota Satu\",\n    \"role\": \"anggota\"\n}",
              "options": { "raw": { "language": "json" } }
            },
            "url": {
              "raw": "{{base_url}}/auth/register",
              "host": ["{{base_url}}"],
              "path": ["auth", "register"]
            }
          },
          "response": []
        },
        // ... tambahkan request lainnya
      ]
    }
  ],
  "variable": [
    { "key": "base_url", "value": "http://localhost:8000" },
    { "key": "token", "value": "" }
  ]
}
```
